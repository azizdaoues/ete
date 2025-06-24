<!DOCTYPE html>
<html>
<head>
    <title>Inscription - Créer votre espace entreprise</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .form-container {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        input, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .domain-preview {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 10px;
            border: 2px solid #e1e5e9;
            color: #667eea;
            font-weight: 500;
            margin-top: 0.5rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .feature {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .feature h3 {
            color: #667eea;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .feature p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #fcc;
        }
        
        .success {
            background: #efe;
            color: #363;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Créez votre espace</h1>
            <p>Rejoignez notre plateforme multi-entreprises</p>
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
            
            @if(session('success'))
                <div class="success">
                    {{ session('success') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <label for="company_name">🏢 Nom de votre entreprise *</label>
                    <input type="text" id="company_name" name="company_name" 
                           value="{{ old('company_name') }}" 
                           placeholder="Ex: Mon Entreprise SARL" required>
                </div>
                
                <div class="form-group">
                    <label for="subdomain">🌐 Votre sous-domaine *</label>
                    <input type="text" id="subdomain" name="subdomain" 
                           value="{{ old('subdomain') }}" 
                           placeholder="mon-entreprise" required>
                    <div class="domain-preview" id="domain-preview">
                        Votre URL : <span id="preview-url">mon-entreprise.localhost:8000</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="admin_name">👤 Nom de l'administrateur *</label>
                    <input type="text" id="admin_name" name="admin_name" 
                           value="{{ old('admin_name') }}" 
                           placeholder="Ex: Jean Dupont" required>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">📧 Email de l'administrateur *</label>
                    <input type="email" id="admin_email" name="admin_email" 
                           value="{{ old('admin_email') }}" 
                           placeholder="admin@votre-entreprise.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">🔒 Mot de passe *</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Minimum 8 caractères" required>
                </div>
                
                <div class="form-group">
                    <label for="password_confirmation">🔒 Confirmer le mot de passe *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           placeholder="Répétez votre mot de passe" required>
                </div>
                
                <div class="form-group">
                    <label for="plan">📦 Plan d'abonnement</label>
                    <select id="plan" name="plan">
                        <option value="free">Gratuit (Essai 30 jours)</option>
                        <option value="basic">Basique - 29€/mois</option>
                        <option value="pro">Professionnel - 79€/mois</option>
                        <option value="enterprise">Entreprise - 199€/mois</option>
                    </select>
                </div>
                
                <button type="submit" class="submit-btn">
                    🚀 Créer mon espace entreprise
                </button>
            </form>
            
            <div class="login-link">
                Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
            </div>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🔒</div>
                    <h3>Sécurisé</h3>
                    <p>Données isolées</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⚡</div>
                    <h3>Rapide</h3>
                    <p>Performance optimisée</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <h3>Simple</h3>
                    <p>Interface intuitive</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Prévisualisation du domaine en temps réel
        const companyName = document.getElementById('company_name');
        const subdomain = document.getElementById('subdomain');
        const previewUrl = document.getElementById('preview-url');
        
        function updatePreview() {
            const company = companyName.value.toLowerCase()
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            
            const sub = subdomain.value.toLowerCase()
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
            
            const finalSubdomain = sub || company;
            previewUrl.textContent = finalSubdomain + '.localhost:8000';
        }
        
        companyName.addEventListener('input', updatePreview);
        subdomain.addEventListener('input', updatePreview);
        
        // Génération automatique du sous-domaine
        companyName.addEventListener('blur', function() {
            if (!subdomain.value) {
                const company = this.value.toLowerCase()
                    .replace(/[^a-z0-9]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                subdomain.value = company;
                updatePreview();
            }
        });
    </script>
</body>
</html> 