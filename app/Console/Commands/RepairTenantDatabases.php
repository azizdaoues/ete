<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class RepairTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:repair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Réparer toutes les bases de données de tenants en ajoutant les tables manquantes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Réparation des bases de données de tenants...');

        $tenants = Tenant::where('is_active', true)->get();
        $repaired = 0;
        $errors = 0;

        foreach ($tenants as $tenant) {
            $this->line("📋 Traitement du tenant: {$tenant->name} ({$tenant->subdomain})");
            
            $databaseName = $tenant->database;
            $exists = DB::select("SHOW DATABASES LIKE ?", [$databaseName]);
            if (!$exists) {
                $this->error("❌ Base de données introuvable: {$databaseName}");
                $errors++;
                continue;
            }
            config(['database.connections.tenant.database' => $databaseName]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            try {
                // Créer les tables manquantes
                $this->createMissingTables();
                
                $this->info("✅ Tenant {$tenant->name} réparé avec succès");
                $repaired++;
                
            } catch (\Exception $e) {
                $this->error("❌ Erreur lors de la réparation de {$tenant->name}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->newLine();
        $this->info("📊 Résumé:");
        $this->info("✅ Tenants réparés: {$repaired}");
        $this->info("❌ Erreurs: {$errors}");
        
        if ($repaired > 0) {
            $this->info("🎉 Réparation terminée avec succès!");
        }
    }

    private function createMissingTables()
    {
        // Table des utilisateurs
        if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('users')) {
            DB::connection('tenant')->statement('
                CREATE TABLE users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    tenant_id INTEGER,
                    role VARCHAR(50) DEFAULT "user",
                    plan VARCHAR(50) DEFAULT "free",
                    is_active BOOLEAN DEFAULT 1,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ');
        }

        // Table des profils utilisateurs
        if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('user_profiles')) {
            DB::connection('tenant')->statement('
                CREATE TABLE user_profiles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    phone VARCHAR(50),
                    position VARCHAR(100),
                    department VARCHAR(100),
                    avatar VARCHAR(255),
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )
            ');
        }

        // Table des paramètres du tenant
        if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('tenant_settings')) {
            DB::connection('tenant')->statement('
                CREATE TABLE tenant_settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    tenant_id INTEGER,
                    setting_key VARCHAR(100) UNIQUE,
                    setting_value TEXT,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ');
        }

        // Table des activités
        if (!DB::connection('tenant')->getSchemaBuilder()->hasTable('activities')) {
            DB::connection('tenant')->statement('
                CREATE TABLE activities (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    action VARCHAR(100),
                    description TEXT,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at DATETIME,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )
            ');
        }
    }
}
