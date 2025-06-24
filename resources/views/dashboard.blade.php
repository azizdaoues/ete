<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $tenant->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #1a202c;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-title {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: #718096;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #718096;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .info-title {
            font-size: 1.2rem;
            color: #2d3748;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f7fafc;
        }

        .info-label {
            color: #718096;
            font-weight: 500;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
        }

        .plan-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .quick-actions {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: transform 0.3s;
            display: block;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-brand">
            🏢 {{ $tenant->name }}
        </div>
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div>{{ $user->name }}</div>
                <small>{{ $user->email }}</small>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn" style="background: none; border: none; cursor: pointer;">🚪 Déconnexion</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <!-- Section de bienvenue -->
        <div class="welcome-section">
            <h1 class="welcome-title">👋 Bienvenue, {{ $user->name }} !</h1>
            <p class="welcome-subtitle">
                Vous êtes connecté à l'espace <strong>{{ $tenant->name }}</strong> 
                @if(isset($settings['plan']))
                    avec le plan <span class="plan-badge">{{ ucfirst($settings['plan']) }}</span>
                @endif
            </p>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number">{{ $userCount }}</div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number">{{ $activityCount }}</div>
                <div class="stat-label">Activités</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚙️</div>
                <div class="stat-number">{{ count($settings) }}</div>
                <div class="stat-label">Paramètres</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🌐</div>
                <div class="stat-number">{{ $tenant->subdomain }}.localhost</div>
                <div class="stat-label">Sous-domaine</div>
            </div>
        </div>

        <!-- Informations et actions -->
        <div class="info-grid">
            <!-- Informations du tenant -->
            <div class="info-card">
                <h3 class="info-title">📋 Informations du tenant</h3>
                <div class="info-item">
                    <span class="info-label">Nom de l'entreprise:</span>
                    <span class="info-value">{{ $tenant->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Sous-domaine:</span>
                    <span class="info-value">{{ $tenant->subdomain }}.localhost</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut:</span>
                    <span class="info-value">
                        @if($tenant->is_active)
                            <span style="color: #38a169;">✅ Actif</span>
                        @else
                            <span style="color: #e53e3e;">❌ Inactif</span>
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Base de données:</span>
                    <span class="info-value">{{ $tenant->database }}</span>
                </div>
            </div>

            <!-- Informations utilisateur -->
            <div class="info-card">
                <h3 class="info-title">👤 Votre profil</h3>
                <div class="info-item">
                    <span class="info-label">Nom:</span>
                    <span class="info-value">{{ $user->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $user->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Rôle:</span>
                    <span class="info-value">
                        @if($user->role === 'admin')
                            <span style="color: #667eea;">👑 Administrateur</span>
                        @else
                            <span style="color: #718096;">👤 Utilisateur</span>
                        @endif
                    </span>
                </div>
                @if(isset($settings['plan']))
                <div class="info-item">
                    <span class="info-label">Plan:</span>
                    <span class="info-value">
                        <span class="plan-badge">{{ ucfirst($settings['plan']) }}</span>
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="quick-actions">
            <h3 class="info-title">⚡ Actions rapides</h3>
            <div class="actions-grid">
                <a href="{{ route('users.index') }}" class="action-btn">
                    👥 Gérer les utilisateurs
                </a>
                <a href="{{ route('users.create') }}" class="action-btn">
                    ➕ Ajouter un utilisateur
                </a>
                <a href="#" class="action-btn">
                    ⚙️ Paramètres du tenant
                </a>
                <a href="#" class="action-btn">
                    📊 Rapports d'activité
                </a>
                <a href="#" class="action-btn">
                    👤 Mon profil
                </a>
                <a href="#" class="action-btn">
                    📝 Créer un document
                </a>
            </div>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="quick-actions" style="margin-top: 2rem;">
            <h3 class="info-title">👥 Utilisateurs de l'entreprise</h3>
            @if($users->count() > 0)
                <div class="users-list">
                    @foreach($users as $userItem)
                        <div class="user-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #e2e8f0; background: #f8f9fa; margin-bottom: 0.5rem; border-radius: 8px;">
                            <div>
                                <strong>{{ $userItem->name }}</strong>
                                <br>
                                <small style="color: #718096;">{{ $userItem->email }}</small>
                                @if($userItem->is_admin)
                                    <span style="background: #667eea; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; margin-left: 0.5rem;">👑 Admin</span>
                                @else
                                    <span style="background: #e2e8f0; color: #4a5568; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; margin-left: 0.5rem;">👤 Utilisateur</span>
                                @endif
                            </div>
                            <div>
                                <small style="color: #718096;">Créé le {{ \Carbon\Carbon::parse($userItem->created_at)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="text-align: center; color: #718096; padding: 2rem;">Aucun utilisateur trouvé. <a href="{{ route('users.create') }}" style="color: #667eea;">Ajouter le premier utilisateur</a></p>
            @endif
        </div>
    </div>
</body>
</html> 