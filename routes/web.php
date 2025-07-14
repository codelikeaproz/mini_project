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
| Public Routes
|--------------------------------------------------------------------------
*/

// Home redirect
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    $user = auth()->user();
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'mdrrmo_staff' => redirect()->route('user.dashboard'),
        default => redirect()->route('dashboard')
    };
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Password reset routes
Route::prefix('password')->name('password.')->group(function () {
    Route::get('/forgot', [PasswordResetController::class, 'showForgotPasswordForm'])->name('request');
    Route::post('/forgot', [PasswordResetController::class, 'sendResetLink'])->name('email');
    Route::get('/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('reset');
    Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->name('update');
});

// Email verification routes
Route::prefix('email')->name('verification.')->group(function () {
    Route::get('/verify/{token}', [EmailVerificationController::class, 'verifyEmail'])->name('verify');
    Route::get('/verify', function () {
        return view('auth.email-verification-notice');
    })->name('notice');
    Route::post('/resend', [EmailVerificationController::class, 'resendVerification'])->name('send');
});

// Two-factor authentication
Route::prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/verify', [TwoFactorController::class, 'showVerifyForm'])->name('verify.form');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
    Route::post('/resend', [TwoFactorController::class, 'resendCode'])->name('resend');
});

/*
|--------------------------------------------------------------------------
| Debug Routes (Remove in production)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/debug-user', function () {
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
    });

    Route::get('/debug-role', function () {
        $user = auth()->user();
        return [
            'role' => $user->role,
            'role_quoted' => "'{$user->role}'",
            'role_length' => strlen($user->role),
            'role_hex' => bin2hex($user->role),
            'expected_roles' => ['admin', 'mdrrmo_staff'],
            'comparisons' => [
                'admin_strict' => $user->role === 'admin',
                'staff_strict' => $user->role === 'mdrrmo_staff',
                'admin_in_array' => in_array($user->role, ['admin']),
                'staff_in_array' => in_array($user->role, ['mdrrmo_staff']),
                'both_in_array' => in_array($user->role, ['admin', 'mdrrmo_staff']),
            ]
        ];
    });

    Route::get('/debug-route-access', function () {
        $user = auth()->user();
        try {
            $routes = [
                'incidents.index' => route('incidents.index'),
                'vehicles.index' => route('vehicles.index'),
                'victims.index' => route('victims.index'),
                'incidents.create' => route('incidents.create'),
                'user.dashboard' => route('user.dashboard'),
                'user.profile' => route('user.profile'),
            ];
        } catch (\Exception $e) {
            $routes = ['error' => $e->getMessage()];
        }

        return [
            'user_role' => $user->role,
            'available_routes' => $routes,
            'expected_access' => 'All routes should work for mdrrmo_staff'
        ];
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Base Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // Basic dashboard (fallback)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Only Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        // Admin dashboard
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Staff registration (admin only)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
            Route::post('/register', [AuthController::class, 'register'])->name('register.store');
            Route::get('/profile', [UserController::class, 'adminProfile'])->name('profile');
            Route::put('/profile', [UserController::class, 'updateAdminProfile'])->name('profile.update');
            Route::get('/login-attempts', [LoginAttemptController::class, 'index'])->name('login-attempts');
        });

        // User management (admin only)
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])->name('users.resend-verification');
    });

    /*
    |--------------------------------------------------------------------------
    | Staff Only Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:mdrrmo_staff')->group(function () {
        // Staff dashboard and profile
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard');
            Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
            Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Shared Routes (Admin OR Staff Access)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Incident Management Routes (Admin - Full Access)
        |--------------------------------------------------------------------------
        */
        Route::resource('incidents', IncidentController::class);

        /*
        |--------------------------------------------------------------------------
        | Vehicle Management Routes (Admin - Full Access)
        |--------------------------------------------------------------------------
        */
        Route::resource('vehicles', VehicleController::class);

        /*
        |--------------------------------------------------------------------------
        | Victim Management Routes (Admin - Full Access)
        |--------------------------------------------------------------------------
        */
        Route::resource('victims', VictimController::class);

    });

    // Staff gets limited access to the same resources
    Route::middleware('role:mdrrmo_staff')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Incident Management Routes (Staff - Limited Access)
        |--------------------------------------------------------------------------
        */
        Route::resource('incidents', IncidentController::class)->except(['destroy']);

        /*
        |--------------------------------------------------------------------------
        | Vehicle Management Routes (Staff - Read Only)
        |--------------------------------------------------------------------------
        */
        Route::resource('vehicles', VehicleController::class)->only(['index', 'show']);

        /*
        |--------------------------------------------------------------------------
        | Victim Management Routes (Staff - Limited Access)
        |--------------------------------------------------------------------------
        */
        Route::resource('victims', VictimController::class)->except(['destroy']);

    });

    /*
    |--------------------------------------------------------------------------
    | Additional Routes (Both Admin and Staff)
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin,mdrrmo_staff')->group(function () {

        // Additional incident routes
        Route::prefix('incidents')->name('incidents.')->group(function () {
            Route::patch('/{incident}/status', [IncidentController::class, 'updateStatus'])->name('update-status');
            Route::post('/{incident}/assign', [IncidentController::class, 'assign'])->name('assign');
            Route::patch('/{incident}/assign-staff', [IncidentController::class, 'assignStaff'])->name('assign-staff');
            Route::patch('/{incident}/assign-vehicle', [IncidentController::class, 'assignVehicle'])->name('assign-vehicle');
            Route::post('/{incident}/update-field', [IncidentController::class, 'updateField'])->name('update-field');
        });

        // Additional vehicle routes (admin only for destructive operations)
        Route::middleware('role:admin')->group(function () {
            Route::prefix('vehicles')->name('vehicles.')->group(function () {
                Route::patch('/{vehicle}/status', [VehicleController::class, 'updateStatus'])->name('update-status');
                Route::patch('/{vehicle}/fuel', [VehicleController::class, 'updateFuel'])->name('update-fuel');
                Route::patch('/{vehicle}/schedule-maintenance', [VehicleController::class, 'scheduleMaintenance'])->name('schedule-maintenance');
                Route::patch('/{vehicle}/complete-maintenance', [VehicleController::class, 'completeMaintenance'])->name('complete-maintenance');
            });
        });

        // Victim management
        Route::prefix('victims')->name('victims.')->group(function () {
            Route::get('/incident/{incident}', [VictimController::class, 'getByIncident'])->name('by-incident');
            Route::get('/{victim}/data', [VictimController::class, 'getVictim'])->name('get-data');
        });

        // Heat Map
        Route::get('/heat-map', function () {
            $totalIncidents = \App\Models\Incident::count();
            return view('heat-map.index', compact('totalIncidents'));
        })->name('heat-map.index');

        /*
        |--------------------------------------------------------------------------
        | API Routes
        |--------------------------------------------------------------------------
        */

        // Incident API
        Route::prefix('api/incidents')->name('api.incidents.')->group(function () {
            Route::get('/', [IncidentController::class, 'apiIndex'])->name('index');
            Route::get('/statistics', [IncidentController::class, 'statistics'])->name('statistics');
            Route::get('/heat-map', [IncidentController::class, 'heatMapData'])->name('heat-map');
            Route::get('/monthly-data', [IncidentController::class, 'monthlyData'])->name('monthly-data');
            Route::get('/type-distribution', [IncidentController::class, 'typeDistribution'])->name('type-distribution');
        });

        // Vehicle API
        Route::prefix('api/vehicles')->name('api.vehicles.')->group(function () {
            Route::get('/available', [VehicleController::class, 'getAvailable'])->name('available');
            Route::get('/statistics', [VehicleController::class, 'getStatistics'])->name('statistics');
            Route::get('/needing-attention', [VehicleController::class, 'getNeedingAttention'])->name('needing-attention');
        });

        // Dashboard API
        Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
            Route::get('/heatmap-data', [DashboardController::class, 'getHeatMapData'])->name('heatmap');
            Route::get('/chart-data', [DashboardController::class, 'getChartData'])->name('charts');
        });
    });
});

// Debug route for incident victims
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
