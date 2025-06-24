<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Le TenantMiddleware a déjà configuré la connexion à la BDD.
        // On peut donc tenter l'authentification directement.
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Stocker l'ID du locataire dans la session
            $tenant = app(Tenant::class);
            if($tenant) {
                $request->session()->put('tenant_id', $tenant->id);
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Les informations d\'identification ne correspondent pas.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        // Le middleware a déjà identifié le locataire et configuré la connexion.
        // On récupère simplement l'instance du locataire via le singleton.
        $tenant = app(Tenant::class);
        $user = Auth::user();

        // S'assurer que le locataire a été trouvé avant de continuer
        if (!$tenant) {
            // Gérer le cas où aucun locataire n'est trouvé, peut-être rediriger.
            return redirect('/login')->withErrors(['error' => 'Impossible de trouver les informations de votre espace.']);
        }

        $userCount = DB::connection('tenant')->table('users')->count();
        $activityCount = DB::connection('tenant')->table('activities')->count();
        $settings = DB::connection('tenant')->table('settings')->get();
        $users = DB::connection('tenant')->table('users')->get();

        return view('dashboard', compact('tenant', 'user', 'userCount', 'activityCount', 'settings', 'users'));
    }

    public function logout(Request $request)
    {
        if (session('tenant_id') && session('user_id')) {
            // Enregistrer l'activité de déconnexion
            try {
                config(['database.connections.tenant.database' => database_path('tenant_' . session('tenant_subdomain') . '.sqlite')]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                
                DB::connection('tenant')->table('activities')->insert([
                    'user_id' => session('user_id'),
                    'action' => 'logout',
                    'description' => 'Déconnexion',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now()
                ]);
            } catch (\Exception $e) {
                // Ignorer les erreurs lors de la déconnexion
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
