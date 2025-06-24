<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bienvenue - Votre Service Multi-Tenant</title>
        <style>
            body {
                font-family: 'Segoe UI', sans-serif;
                margin: 0;
                background-color: #f4f7f6;
                color: #333;
            }
            .hero {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 6rem 2rem;
                text-align: center;
            }
            .hero h1 {
                font-size: 3.5rem;
                margin-bottom: 1rem;
            }
            .hero p {
                font-size: 1.25rem;
                max-width: 600px;
                margin: 0 auto 2rem auto;
                opacity: 0.9;
            }
            .cta-button {
                background-color: white;
                color: #667eea;
                padding: 1rem 2.5rem;
                border-radius: 50px;
                text-decoration: none;
                font-weight: bold;
                font-size: 1.1rem;
                transition: all 0.3s ease;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            }
            .cta-button:hover {
                transform: translateY(-3px);
                box-shadow: 0 15px 25px rgba(0,0,0,0.15);
            }
            .features {
                padding: 4rem 2rem;
                text-align: center;
            }
            .features h2 {
                font-size: 2.5rem;
                margin-bottom: 3rem;
            }
            .feature-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 2rem;
                max-width: 1100px;
                margin: 0 auto;
            }
            .feature {
                background: white;
                padding: 2rem;
                border-radius: 15px;
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            }
            .feature .icon {
                font-size: 3rem;
                color: #667eea;
                margin-bottom: 1rem;
            }
            .feature h3 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            .footer {
                background-color: #333;
                color: white;
                text-align: center;
                padding: 2rem;
                margin-top: 4rem;
            }
            .top-nav {
                position: absolute;
                top: 0;
                right: 0;
                padding: 1.5rem;
            }
            .top-nav a {
                color: white;
                text-decoration: none;
                font-weight: bold;
                margin-left: 1.5rem;
            }
        </style>
    </head>
    <body>
        <div class="top-nav">
            <a href="{{ route('tenant.register') }}">S'inscrire</a>
        </div>

        <div class="hero">
            <h1>Lancez votre Propre Service, Simplement.</h1>
            <p>Notre plateforme multi-tenant vous offre une isolation complète des données, une configuration instantanée et une gestion simplifiée. Concentrez-vous sur votre métier, nous nous occupons de la technique.</p>
            <a href="{{ route('tenant.register') }}" class="cta-button">Commencer Gratuitement</a>
        </div>

        <div class="features">
            <h2>Pourquoi nous choisir ?</h2>
            <div class="feature-grid">
                <div class="feature">
                    <div class="icon">🚀</div>
                    <h3>Déploiement Instantané</h3>
                    <p>Créez votre espace personnalisé en quelques clics et commencez à travailler immédiatement.</p>
                </div>
                <div class="feature">
                    <div class="icon">🔒</div>
                    <h3>Données Isolées</h3>
                    <p>Chaque client dispose de sa propre base de données, garantissant une sécurité et une confidentialité maximales.</p>
                </div>
                <div class="feature">
                    <div class="icon">⚙️</div>
                    <h3>Gestion Simplifiée</h3>
                    <p>Un tableau de bord intuitif pour gérer vos utilisateurs et vos paramètres en toute simplicité.</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Votre Service Inc. Tous droits réservés.</p>
        </div>
    </body>
</html>
