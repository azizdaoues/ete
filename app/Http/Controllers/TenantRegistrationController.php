<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class TenantRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.tenant-register');
    }

    public function showSuccessPage(Request $request)
    {
        // Assurez-vous que les données sont bien passées en session flash
        if (!session('tenant_name') || !session('login_url') || !session('admin_email')) {
            return redirect()->route('tenant.register');
        }

        return view('auth.tenant-success', [
            'tenant_name' => session('tenant_name'),
            'login_url' => session('login_url'),
            'admin_email' => session('admin_email'),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|alpha_dash|unique:tenants,subdomain',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255',
            'admin_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            return redirect()->route('tenant.register')
                        ->withErrors($validator)
                        ->withInput();
        }

        $validated = $validator->validated();
        $subdomain = $validated['subdomain'];
        $databaseName = 'tenant_' . $subdomain;

        try {
            // 1. Création de la base de données (hors transaction)
            $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            if ($exists) {
                return redirect()->route('tenant.register')
                    ->withErrors(['subdomain' => 'Ce sous-domaine est déjà utilisé.'])
                    ->withInput();
            }
            DB::statement("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Création du tenant (transaction sur la connexion principale)
            DB::beginTransaction();
            $tenant = Tenant::create([
                'name' => $validated['company_name'],
                'subdomain' => $subdomain,
                'database' => $databaseName,
                'is_active' => true,
            ]);
            DB::commit();

            // 3. Configurer la connexion et lancer les migrations (hors transaction)
            Config::set('database.connections.tenant.database', $databaseName);
            DB::purge('tenant');
            DB::reconnect('tenant');
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations',
                '--force' => true,
            ]);

            // 4. Création de l'admin (transaction sur la connexion tenant)
            DB::connection('tenant')->beginTransaction();
            DB::connection('tenant')->table('users')->insert([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'is_admin' => true,
                'tenant_id' => $tenant->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::connection('tenant')->commit();

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            if (DB::connection('tenant')->transactionLevel() > 0) {
                DB::connection('tenant')->rollBack();
            }
            return redirect()->route('tenant.register')
                ->withErrors(['error' => 'Une erreur est survenue lors de la création de votre espace: ' . $e->getMessage()])
                ->withInput();
        }

        $domain = config('app.domain', 'localhost');
        $port = $request->getPort();
        $loginUrl = "http://{$subdomain}.{$domain}:{$port}/login";

        // Mettre les informations en session flash pour la page de succès
        return redirect()->route('tenant.register.success')->with([
            'tenant_name' => $tenant->name,
            'login_url' => $loginUrl,
            'admin_email' => $request->admin_email,
        ]);
    }
}
