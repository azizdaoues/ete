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

        DB::beginTransaction();
        try {
            $tenant = $this->createTenant($validator->validated());
            $this->createTenantAdmin($tenant, $validator->validated());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('tenant.register')
                        ->withErrors(['error' => 'Une erreur est survenue lors de la création de votre espace: ' . $e->getMessage()])
                        ->withInput();
        }

        $domain = config('app.domain', 'localhost');
        $port = $request->getPort();
        $loginUrl = "http://{$tenant->subdomain}.{$domain}:{$port}/login";

        // Mettre les informations en session flash pour la page de succès
        return redirect()->route('tenant.register.success')->with([
            'tenant_name' => $tenant->name,
            'login_url' => $loginUrl,
            'admin_email' => $request->admin_email,
        ]);
    }

    private function createTenant(array $validatedData): Tenant
    {
        $tenant = Tenant::create([
            'name' => $validatedData['company_name'],
            'subdomain' => $validatedData['subdomain'],
            'database' => 'tenant_' . $validatedData['subdomain'],
            'is_active' => true,
        ]);
        
        $databaseName = $tenant->database;
        DB::statement("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations',
            '--force' => true,
        ]);
        
        return $tenant;
    }

    private function createTenantAdmin(Tenant $tenant, array $validatedData): void
    {
        DB::connection('tenant')->table('users')->insert([
            'name' => $validatedData['admin_name'],
            'email' => $validatedData['admin_email'],
            'password' => Hash::make($validatedData['admin_password']),
            'is_admin' => true,
            'tenant_id' => $tenant->id, // Important for consistency
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
