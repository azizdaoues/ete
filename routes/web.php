<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TenantRegistrationController;

// Route d'accueil
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes d'inscription du locataire
Route::get('/register/tenant', [TenantRegistrationController::class, 'showRegistrationForm'])->name('tenant.register');
Route::post('/register/tenant', [TenantRegistrationController::class, 'register'])->name('tenant.register.submit');
Route::get('/register/tenant/success', [TenantRegistrationController::class, 'showSuccessPage'])->name('tenant.register.success');

// Routes d'inscription
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Route du dashboard (protégée par le middleware d'authentification)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Routes de gestion des utilisateurs
    Route::resource('users', UserController::class);

    // Routes d'administration (protégées par middleware auth et admin)
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Routes admin spécifiques si nécessaire
    });
});

// Routes d'authentification (seulement pour les sous-domaines)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
