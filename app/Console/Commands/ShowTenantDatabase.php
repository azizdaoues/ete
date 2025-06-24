<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShowTenantDatabase extends Command
{
    protected $signature = 'tenant:show-database {subdomain}';
    protected $description = 'Affiche le contenu de la base de données d\'un tenant spécifique';

    public function handle()
    {
        $subdomain = $this->argument('subdomain');
        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            $this->error("Tenant avec le sous-domaine '{$subdomain}' non trouvé.");
            return;
        }

        // Configuration de la connexion à la base de données du tenant
        config(['database.connections.tenant.database' => $tenant->database]);
        DB::purge('tenant');

        $this->info("Base de données pour le tenant '{$tenant->name}' ({$tenant->subdomain}):");
        $this->info("----------------------------------------");

        // Récupérer toutes les tables
        $tables = DB::connection('tenant')->select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $this->info("\nTable: {$tableName}");
            $this->info("----------------------------------------");

            // Récupérer les données de la table
            $rows = DB::connection('tenant')->table($tableName)->get();

            if ($rows->isEmpty()) {
                $this->info("Aucune donnée dans cette table.");
                continue;
            }

            // Afficher les en-têtes
            $headers = array_keys((array) $rows->first());
            $this->table($headers, $rows->map(function ($row) {
                return (array) $row;
            }));
        }
    }
} 