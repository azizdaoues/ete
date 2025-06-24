<?php

namespace App\Console\Commands\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crée un nouveau locataire avec sa base de données et son utilisateur admin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ... (collecte des informations)
        $subdomain = $this->ask('Entrez le sous-domaine pour le nouveau locataire');
        $email = $this->ask('Entrez l\'email pour l\'utilisateur administrateur');
        $password = $this->secret('Entrez le mot de passe pour l\'utilisateur administrateur');

        try {
            // Validation
            $validator = Validator::make([
                'subdomain' => $subdomain,
                'email' => $email,
                'password' => $password,
            ], [
                'subdomain' => 'required|string|unique:tenants,subdomain',
                'email' => 'required|email',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Créer le locataire dans la base de données principale
            $tenant = Tenant::create(['subdomain' => $subdomain]);
            $this->info("Locataire '{$subdomain}' créé avec l'ID {$tenant->id}.");

            // S'assurer que le répertoire des bases de données existe
            $tenantsPath = database_path('tenants');
            if (!File::isDirectory($tenantsPath)) {
                File::makeDirectory($tenantsPath, 0755, true, true);
                $this->info("Répertoire des locataires créé : {$tenantsPath}");
            }

            // Créer le fichier de base de données
            $dbPath = database_path('tenants/' . $tenant->id . '.sqlite');
            File::put($dbPath, ''); // Crée un fichier vide
            $this->info("Fichier de base de données créé : {$dbPath}");

            // Configurer la connexion pour la migration et le seeder
            $databaseName = 'tenant_' . $subdomain;
            DB::statement("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            Config::set('database.connections.tenant.database', $databaseName);
            DB::purge('tenant');
            DB::reconnect('tenant');
            $this->info("Connexion 'tenant' configurée pour pointer vers la nouvelle base de données.");

            // Exécuter les migrations sur la base de données du locataire
            $this->call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations',
                '--realpath' => true,
            ]);
            $this->info("Migrations exécutées sur la base de données du locataire.");

            // Créer l'utilisateur admin dans la base de données du locataire
            User::on('tenant')->create([
                'name' => 'Admin ' . $subdomain,
                'email' => $email,
                'password' => Hash::make($password),
                'tenant_id' => $tenant->id,
                'is_admin' => true,
            ]);
            $this->info("Utilisateur administrateur créé pour le locataire.");

            $this->info("Le locataire a été créé avec succès !");
            $this->info("URL d'accès : http://{$subdomain}.localhost:8000"); // Adaptez si nécessaire

        } catch (ValidationException $e) {
            $this->error('La création du locataire a échoué. Veuillez corriger les erreurs suivantes :');
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->error("- {$message}");
                }
            }
            return 1;
        } catch (\Exception $e) {
            $this->error("Une erreur inattendue est survenue : " . $e->getMessage());
            // Nettoyage en cas d'erreur
            if (isset($tenant)) {
                $tenant->delete();
            }
            return 1;
        }

        return 0;
    }
}
