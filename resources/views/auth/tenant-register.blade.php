<!DOCTYPE html>
<html>
<head>
    <title>Créer votre Espace - Laravel Multi-Tenant</title>
    <style>
        /* Using the same styles as the login page for consistency */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); max-width: 500px; width: 100%; overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; text-align: center; }
        .header h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .header p { opacity: 0.9; font-size: 1.1rem; }
        .form-container { padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: 500; }
        input { width: 100%; padding: 12px 16px; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease; }
        input:focus { outline: none; border-color: #667eea; }
        .submit-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 1.1rem; cursor: pointer; width: 100%; transition: transform 0.3s ease; }
        .submit-btn:hover { transform: translateY(-2px); }
        .login-link { text-align: center; margin-top: 1.5rem; color: #666; }
        .login-link a { color: #667eea; text-decoration: none; font-weight: 500; }
        .login-link a:hover { text-decoration: underline; }
        .error { background: #fee; color: #c33; padding: 10px; border-radius: 5px; margin-bottom: 1rem; border: 1px solid #fcc; }
        .error ul { list-style-position: inside; padding-left: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Créez votre Espace</h1>
            <p>Rejoignez-nous et lancez votre service en quelques secondes.</p>
        </div>
        
        <div class="form-container">
            @if($errors->any())
                <div class="error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('tenant.register.submit') }}">
                @csrf
                
                <div class="form-group">
                    <label for="company_name">Nom de votre entreprise *</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Ex: Ma Super Entreprise" required>
                </div>

                <div class="form-group">
                    <label for="subdomain">Sous-domaine souhaité *</label>
                    <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" placeholder="Ex: masuperentreprise" required>
                    <small style="color: #666; display: block; margin-top: 5px;">Votre URL sera : <span style="font-weight: bold; color: #667eea;">votresousdomaine</span>.localhost:8000</small>
                </div>

                <hr style="border: 1px solid #eee; margin: 2rem 0;">

                <div class="form-group">
                    <label for="admin_name">Votre nom (Administrateur) *</label>
                    <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="admin_email">Votre email (Administrateur) *</label>
                    <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                </div>

                <div class="form-group">
                    <label for="admin_password">Votre mot de passe (Administrateur) *</label>
                    <input type="password" id="admin_password" name="admin_password" required>
                </div>

                <div class="form-group">
                    <label for="admin_password_confirmation">Confirmez votre mot de passe *</label>
                    <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required>
                </div>
                
                <button type="submit" class="submit-btn">Créer mon Espace</button>
            </form>
        </div>
    </div>
</body>
</html> 