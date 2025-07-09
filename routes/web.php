<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LoginAttemptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| MDRRMO Authentication Routes
|--------------------------------------------------------------------------
| Authentication routes for MDRRMO Accident Reporting System
*/

// Public routes
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Admin-only registration (moved to admin routes section)
// Public registration is disabled for MDRRMO security

// Dashboard route (protected)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])
    ->name('verify.email');
Route::get('/email/verify', function () {
    return view('auth.email-verification-notice');
})->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resendVerification'])
    ->name('verification.send');

/*
|--------------------------------------------------------------------------
| Two-Factor Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])
    ->name('2fa.verify.form');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])
    ->name('2fa.verify');
Route::post('/2fa/resend', [TwoFactorController::class, 'resendCode'])
    ->name('2fa.resend');

/*
|--------------------------------------------------------------------------
| Admin Routes - MDRRMO System Management
|--------------------------------------------------------------------------
| Protected by: auth, verified, role:admin middleware
*/

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Admin-only staff registration routes
    Route::get('/admin/register', [AuthController::class, 'showRegister'])->name('admin.register');
    Route::post('/admin/register', [AuthController::class, 'register'])->name('admin.register.store');

    // User management routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
    });

    // Admin profile and security monitoring
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [UserController::class, 'adminProfile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateAdminProfile'])->name('profile.update');
        Route::get('/login-attempts', [LoginAttemptController::class, 'index'])->name('login-attempts');
    });
});

/*
|--------------------------------------------------------------------------
| MDRRMO Staff Routes
|--------------------------------------------------------------------------
| Protected by: auth, verified, role:mdrrmo_staff middleware
*/

Route::middleware(['auth', 'verified', 'role:mdrrmo_staff'])->group(function () {
    // MDRRMO Staff dashboard and profile
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    });
});

/*
|--------------------------------------------------------------------------
| Incident Management Routes
|--------------------------------------------------------------------------
| Protected by: auth, verified, role:admin,mdrrmo_staff middleware
*/

Route::middleware(['auth', 'verified', 'role:admin,mdrrmo_staff'])->group(function () {
    // Incident CRUD routes
    Route::resource('incidents', IncidentController::class);

    // Additional incident routes
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::patch('/{incident}/status', [IncidentController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{incident}/assign-staff', [IncidentController::class, 'assignStaff'])->name('assign-staff');
        Route::patch('/{incident}/assign-vehicle', [IncidentController::class, 'assignVehicle'])->name('assign-vehicle');
    });

    // API routes for incidents
    Route::prefix('api/incidents')->name('api.incidents.')->group(function () {
        Route::get('/', [IncidentController::class, 'apiIndex'])->name('index');
        Route::get('/statistics', [IncidentController::class, 'statistics'])->name('statistics');
        Route::get('/heat-map', [IncidentController::class, 'heatMapData'])->name('heat-map');
        Route::get('/monthly-data', [IncidentController::class, 'monthlyData'])->name('monthly-data');
        Route::get('/type-distribution', [IncidentController::class, 'typeDistribution'])->name('type-distribution');
    });

    // API routes for dashboard charts and heat map
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/heatmap-data', [DashboardController::class, 'getHeatMapData'])->name('heatmap');
        Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('charts');
    });

    /*
    |--------------------------------------------------------------------------
    | Vehicle Management Routes
    |--------------------------------------------------------------------------
    */

    // Vehicle CRUD routes
    Route::resource('vehicles', VehicleController::class);

    // Additional vehicle routes
    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::patch('/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{vehicle}/fuel', [VehicleController::class, 'updateFuel'])->name('update-fuel');
        Route::patch('/{vehicle}/schedule-maintenance', [VehicleController::class, 'scheduleMaintenance'])->name('schedule-maintenance');
        Route::patch('/{vehicle}/complete-maintenance', [VehicleController::class, 'completeMaintenance'])->name('complete-maintenance');
    });

    // API routes for vehicles
    Route::prefix('api/vehicles')->name('api.vehicles.')->group(function () {
        Route::get('/available', [VehicleController::class, 'getAvailable'])->name('available');
        Route::get('/statistics', [VehicleController::class, 'getStatistics'])->name('statistics');
        Route::get('/needing-attention', [VehicleController::class, 'getNeedingAttention'])->name('needing-attention');
    });
});



