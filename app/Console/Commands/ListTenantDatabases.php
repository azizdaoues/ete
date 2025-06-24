<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ListTenantDatabases extends Command
{
    protected $signature = 'tenants:list-databases';
    protected $description = 'Liste toutes les bases de données des tenants';

    public function handle()
    {
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->info('Aucun tenant trouvé.');
            return;
        }

        $this->table(
            ['ID', 'Nom', 'Sous-domaine', 'Base de données', 'Statut'],
            $tenants->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'subdomain' => $tenant->subdomain,
                    'database' => $tenant->database,
                    'status' => $tenant->is_active ? 'Actif' : 'Inactif'
                ];
            })
        );
    }
} 