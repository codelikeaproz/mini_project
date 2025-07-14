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
use App\Http\Controllers\VictimController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| MDRRMO Authentication Routes
|--------------------------------------------------------------------------
| Authentication routes for MDRRMO Accident Reporting System
*/

// Public routes
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'mdrrmo_staff') {
        return redirect()->route('user.dashboard');
    }

    return redirect()->route('dashboard'); // fallback
});

// Debug route to check user status
Route::get('/debug-user', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }

    $user = auth()->user();
    return [
        'id' => $user->id,
        'email' => $user->email,
        'role' => $user->role,
        'is_verified' => $user->is_verified,
        'is_active' => $user->is_active,
        'hasVerifiedEmail' => $user->hasVerifiedEmail(),
        'email_verified_at' => $user->email_verified_at,
    ];
})->middleware('auth');

// Debug route to check role issues
Route::get('/debug-role', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }

    $user = auth()->user();
    return [
        'role' => $user->role,
        'role_length' => strlen($user->role),
        'role_bytes' => bin2hex($user->role),
        'expected_roles' => ['admin', 'mdrrmo_staff'],
        'in_array_check_admin' => in_array($user->role, ['admin']),
        'in_array_check_staff' => in_array($user->role, ['mdrrmo_staff']),
        'in_array_check_both' => in_array($user->role, ['admin', 'mdrrmo_staff']),
        'strict_comparison_admin' => $user->role === 'admin',
        'strict_comparison_staff' => $user->role === 'mdrrmo_staff',
    ];
})->middleware('auth');

// Detailed debug route
Route::get('/debug-user-detailed', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }

    $user = auth()->user();
    return [
        'id' => $user->id,
        'email' => $user->email,
        'role' => "'" . $user->role . "'", // wrapped in quotes to see spaces
        'role_raw' => bin2hex($user->role), // hex to see hidden characters
        'is_verified' => $user->is_verified,
        'is_active' => $user->is_active,
        'email_verified_at' => $user->email_verified_at,
        'hasVerifiedEmail' => method_exists($user, 'hasVerifiedEmail') ? $user->hasVerifiedEmail() : 'method not found',
        'account_locked' => method_exists($user, 'isAccountLocked') ? $user->isAccountLocked() : 'method not found'
    ];
})->middleware('auth');

// Test route access for staff
Route::get('/debug-route-access', function () {
    if (!auth()->check()) {
        return 'Not logged in';
    }

    $user = auth()->user();
    $routes = [
        'incidents.index' => route('incidents.index'),
        'vehicles.index' => route('vehicles.index'),
        'victims.index' => route('victims.index'),
        'incidents.create' => route('incidents.create'),
        'user.dashboard' => route('user.dashboard'),
        'user.profile' => route('user.profile'),
    ];

    return [
        'user_role' => $user->role,
        'available_routes' => $routes,
        'expected_access' => 'All routes should work for mdrrmo_staff'
    ];
})->middleware('auth');

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
        Route::post('/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('resend-verification');
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
        Route::post('/{incident}/assign', [IncidentController::class, 'assign'])->name('assign');
        Route::patch('/{incident}/assign-staff', [IncidentController::class, 'assignStaff'])->name('assign-staff');
        Route::patch('/{incident}/assign-vehicle', [IncidentController::class, 'assignVehicle'])->name('assign-vehicle');
        Route::post('/{incident}/update-field', [IncidentController::class, 'updateField'])->name('update-field');
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

    // Heat Map Visualization Route
    Route::get('/heat-map', function () {
        $totalIncidents = \App\Models\Incident::count();
        return view('heat-map.index', compact('totalIncidents'));
    })->name('heat-map.index');

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

    /*
    |--------------------------------------------------------------------------
    | Victim Management Routes
    |--------------------------------------------------------------------------
    */

    // Victim CRUD routes
    Route::resource('victims', VictimController::class);

    // Additional victim routes
    Route::prefix('victims')->name('victims.')->group(function () {
        Route::get('/incident/{incident}', [VictimController::class, 'getByIncident'])->name('by-incident');
        Route::get('/{victim}/data', [VictimController::class, 'getVictim'])->name('get-data');
    });
});

// Temporary debug route
Route::get('/debug/incident/{id}/victims', function($id) {
    $incident = App\Models\Incident::findOrFail($id);
    $victims = $incident->victims;

    return response()->json([
        'incident_id' => $id,
        'victims_count' => $victims->count(),
        'victims' => $victims->map(function($victim) {
            return [
                'id' => $victim->id,
                'name' => $victim->full_name,
                'involvement_type' => $victim->involvement_type,
                'injury_status' => $victim->injury_status
            ];
        })
    ]);
})->middleware(['auth', 'verified']);



