<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        $subdomain = $parts[0];

        // Si on est sur le domaine principal, on laisse passer (page d'accueil)
        if (in_array($subdomain, ['www', 'localhost', '127'])) {
            // Mais on redirige les tentatives de connexion vers la page d'accueil
            if ($request->path() === 'login') {
                return redirect('/');
            }
            return $next($request);
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        // Si le locataire n'existe pas, rediriger vers la page d'accueil
        if (!$tenant) {
            return redirect('http://localhost:8000');
        }

        // Si on est sur un sous-domaine valide, rediriger vers la page de connexion
        if ($request->path() === '/') {
            return redirect('/login');
        }

        // Si le locataire est trouvé, configure sa base de données
        if ($tenant) {
            config(['database.connections.tenant.database' => $tenant->database]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            config(['database.default' => 'tenant']);
            app()->singleton(Tenant::class, function () use ($tenant) {
                return $tenant;
            });

            // Lancer les migrations Laravel pour créer les tables
            \Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations',
                '--realpath' => true,
            ]);
        }
        // Si aucun locataire n'est trouvé, on ne fait rien et on continue.
        // La route sera responsable de gérer le cas où un locataire était attendu.

        return $next($request);
    }
}
