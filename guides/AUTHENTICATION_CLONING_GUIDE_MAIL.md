# Mail Classes and Notifications

## 🎯 **Step 5: Create Mail Classes**

### **5.1 Create Mail Classes**
```bash
php artisan make:mail TwoFactorCodeMail
```

**app/Mail/TwoFactorCodeMail.php**
```php
<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->markdown('emails.two-factor')
            ->subject('Your 2FA Code - ' . config('app.name'))
            ->with(['two_factor_code' => $this->user->two_factor_code]);
    }
}
```

## 🎯 **Step 6: Create Notifications**

### **6.1 Create Notification Classes**
```bash
php artisan make:notification EmailVerificationNotification
php artisan make:notification ResetPasswordNotification
```

**app/Notifications/EmailVerificationNotification.php**
```php
<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = url("/verify-email/" . $this->user->verification_token);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('Thank you for registering with ' . config('app.name') . '.')
            ->line('Please click the button below to verify your email address:')
            ->action('Verify Email', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->line('This verification link will expire in 24 hours.')
            ->salutation('Thank you for using ' . config('app.name') . '!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
```

**app/Notifications/ResetPasswordNotification.php**
```php
<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Thank you for using ' . config('app.name') . '!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
```

## 🎯 **Step 7: Create Email Templates**

### **7.1 Create Email View Templates**
Create these directories and files:
```
resources/views/emails/
├── two-factor.blade.php
└── verify.blade.php
```

**resources/views/emails/two-factor.blade.php**
```php
@component('mail::message')
# Two-Factor Authentication Code

Hello {{ $user->name ?? 'User' }},

Your 2FA verification code is:

@component('mail::panel')
**{{ str_pad($two_factor_code, 6, '0', STR_PAD_LEFT) }}**
@endcomponent

This code will expire in **10 minutes**.

If you did not request this code, please ignore this email and contact support if you have concerns about your account security.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

**resources/views/emails/verify.blade.php**
```php
@component('mail::message')
# Email Verification

Hello {{ $user->name }},

Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address:

@component('mail::button', ['url' => url("/verify-email/" . $user->verification_token)])
Verify Email Address
@endcomponent

If you did not create an account, no further action is required.

This verification link will expire in 24 hours.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
```

## 🎯 **Step 8: Create Traits**

### **8.1 Create Activity Logging Trait**
```bash
mkdir -p app/Traits
```

**app/Traits/LogsActivity.php**
```php
<?php
namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

trait LogsActivity
{
    /**
     * Log user activity
     */
    public static function logActivity($action, $user = null, $details = [])
    {
        try {
            $request = request();
            $ipAddress = $request ? $request->ip() : null;
            $userAgent = $request ? $request->header('User-Agent') : null;

            $log = ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'model_type' => get_class($user ?: new static),
                'model_id' => $user ? $user->id : 0,
                'details' => json_encode($details),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            Log::info("Activity logged successfully", [
                'action' => $action,
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress
            ]);

            return $log;
        } catch (\Exception $e) {
            Log::error("Failed to log activity: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Log authentication activities
     */
    public static function logAuthActivity($action, $user = null, $details = [])
    {
        try {
            $request = request();
            $ipAddress = $request ? $request->ip() : null;
            $userAgent = $request ? $request->header('User-Agent') : null;

            if ($user) {
                $details['user_name'] = $user->name;
                $details['user_email'] = $user->email;
                $details['user_role'] = $user->role ?? 'user';
            }

            $log = ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'model_type' => 'Authentication',
                'model_id' => $user ? $user->id : 0,
                'details' => json_encode($details),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);

            Log::info("Authentication activity logged successfully", [
                'action' => $action,
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress
            ]);

            return $log;
        } catch (\Exception $e) {
            Log::error("Failed to log authentication activity: " . $e->getMessage());
            return null;
        }
    }
}
```

## 🎯 **Step 9: Routes Configuration**

### **9.1 Create Complete Routes File**
**routes/web.php**
```php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LoginAttemptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;

// Public routes
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Dashboard route (protected)
Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

// Password Reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
    ->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

// Email Verification routes
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verifyEmail'])
    ->name('verify.email');
Route::get('/email/verify', function () { 
    return view('auth.email-verification-notice'); 
})->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resendVerification'])
    ->name('verification.send');

// Two-Factor Authentication routes
Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])
    ->name('2fa.verify.form');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])
    ->name('2fa.verify');
Route::post('/2fa/resend', [TwoFactorController::class, 'resendCode'])
    ->name('2fa.resend');

// Admin routes (protected by admin role)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
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
    
    // Admin profile and login attempts
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [UserController::class, 'adminProfile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateAdminProfile'])->name('profile.update');
        Route::get('/login-attempts', [LoginAttemptController::class, 'index'])->name('login-attempts');
    });
});

// User routes (protected by user role)
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    // User dashboard and profile
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
    });
});
```

## 🎯 **Step 10: Seeders**

### **10.1 Create User Seeder**
```bash
php artisan make:seeder UserSeeder
```

**database/seeders/UserSeeder.php**
```php
<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create test users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'user',
            'password' => Hash::make('password123'),
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'user',
            'password' => Hash::make('password123'),
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
```

**database/seeders/DatabaseSeeder.php**
```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
```

This completes the backend implementation. Would you like me to continue with the Views (Blade templates) section next? 
