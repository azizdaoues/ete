<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class RegisterController extends Controller
{
    private $currentTenantDatabase;

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validation des données
        $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:50|unique:tenants,subdomain|regex:/^[a-z0-9-]+$/',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'plan' => 'required|in:free,basic,pro,enterprise'
        ], [
            'subdomain.unique' => 'Ce sous-domaine est déjà pris. Veuillez en choisir un autre.',
            'subdomain.regex' => 'Le sous-domaine ne peut contenir que des lettres minuscules, chiffres et tirets.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.'
        ]);

        try {
            DB::beginTransaction();

            // Nettoyer le sous-domaine
            $subdomain = Str::slug($request->subdomain);
            $this->currentTenantDatabase = 'tenant_' . $subdomain;

            // Vérifier si la base existe déjà
            $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$this->currentTenantDatabase]);
            if ($exists) {
                return back()->withErrors(['subdomain' => 'Ce sous-domaine est déjà utilisé.'])->withInput();
            }

            // Créer la base de données MySQL
            DB::statement("CREATE DATABASE `{$this->currentTenantDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Créer le tenant
            $tenant = Tenant::create([
                'name' => $request->company_name,
                'subdomain' => $subdomain,
                'database' => $this->currentTenantDatabase,
                'is_active' => true
            ]);

            // Configurer la connexion à la base de données du tenant
            config(['database.connections.tenant.database' => $this->currentTenantDatabase]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            // Lancer les migrations Laravel
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations',
                '--force' => true,
            ]);

            // Créer l'utilisateur administrateur
            $userId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'role' => 'admin',
                'plan' => $request->plan,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Créer les tables de configuration du tenant
            $this->createTenantConfigTables($tenant->id, $request->plan);

            DB::commit();

            // Message de succès avec les informations de connexion
            $successMessage = "🎉 Votre espace entreprise a été créé avec succès !\n\n";
            $successMessage .= "🏢 Entreprise : {$request->company_name}\n";
            $successMessage .= "🌐 URL : {$subdomain}.localhost:8000\n";
            $successMessage .= "👤 Admin : {$request->admin_email}\n";
            $successMessage .= "📦 Plan : " . ucfirst($request->plan) . "\n\n";
            $successMessage .= "Vous pouvez maintenant vous connecter avec votre email et mot de passe.";

            return redirect()->route('login')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la création de votre espace. Veuillez réessayer.'])->withInput();
        }
    }

    private function createTenantConfigTables($tenantId, $plan)
    {
        // Insérer les paramètres par défaut du tenant
        $defaultSettings = [
            ['tenant_id' => $tenantId, 'setting_key' => 'company_name', 'setting_value' => 'Mon Entreprise'],
            ['tenant_id' => $tenantId, 'setting_key' => 'plan', 'setting_value' => $plan],
            ['tenant_id' => $tenantId, 'setting_key' => 'max_users', 'setting_value' => $this->getMaxUsers($plan)],
            ['tenant_id' => $tenantId, 'setting_key' => 'storage_limit', 'setting_value' => $this->getStorageLimit($plan)],
            ['tenant_id' => $tenantId, 'setting_key' => 'created_at', 'setting_value' => now()],
        ];

        foreach ($defaultSettings as $setting) {
            DB::connection('tenant')->table('tenant_settings')->insert([
                'tenant_id' => $setting['tenant_id'],
                'setting_key' => $setting['setting_key'],
                'setting_value' => $setting['setting_value'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    private function getMaxUsers($plan)
    {
        return match($plan) {
            'free' => 5,
            'basic' => 25,
            'pro' => 100,
            'enterprise' => -1, // Illimité
            default => 5
        };
    }

    private function getStorageLimit($plan)
    {
        return match($plan) {
            'free' => '1GB',
            'basic' => '10GB',
            'pro' => '100GB',
            'enterprise' => '1TB',
            default => '1GB'
        };
    }
} 