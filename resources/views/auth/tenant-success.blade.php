<!DOCTYPE html>
<html>
<head>
    <title>Inscription Réussie ! - Laravel Multi-Tenant</title>
    <style>
        /* Using the same styles for consistency */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); max-width: 550px; width: 100%; overflow: hidden; text-align: center; padding: 3rem; }
        .icon { font-size: 5rem; color: #34D399; margin-bottom: 1.5rem; }
        h1 { color: #333; font-size: 2.2rem; margin-bottom: 1rem; }
        p { color: #666; font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem; }
        .info-box { background: #f8f9fa; border: 1px solid #e1e5e9; border-radius: 10px; padding: 1.5rem; margin-bottom: 2rem; text-align: left; }
        .info-box strong { display: block; color: #333; margin-bottom: 0.5rem; }
        .info-box code { background: #e8f4fd; color: #0066cc; padding: 0.5rem 1rem; border-radius: 5px; display: block; word-break: break-all; }
        .login-btn { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 1.1rem; text-decoration: none; transition: transform 0.3s ease; }
        .login-btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🎉</div>
        <h1>Félicitations, {{ $tenant_name }} !</h1>
        <p>Votre espace a été créé avec succès. Vous pouvez maintenant vous connecter et commencer à utiliser notre service.</p>

        <div class="info-box">
            <strong>Votre URL de connexion personnalisée :</strong>
            <code><a href="{{ $login_url }}" target="_blank">{{ $login_url }}</a></code>
        </div>

        <div class="info-box">
            <strong>Votre email d'administrateur :</strong>
            <code>{{ $admin_email }}</code>
        </div>
        
        <p>Utilisez le mot de passe que vous avez défini lors de l'inscription pour vous connecter.</p>

        <a href="{{ $login_url }}" class="login-btn">Accéder à mon espace</a>
    </div>
</body>
</html> 