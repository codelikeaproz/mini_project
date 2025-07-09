# Authentication System Cloning Guide

## 🎯 **Overview**
This guide will help you clone the complete authentication system with role-based access control, email verification, 2FA, and comprehensive security features.

## 📋 **Prerequisites**
- Fresh Laravel 11 installation
- Database configured
- Mail service configured (Gmail/Mailtrap)
- Composer installed

---

## 🏗️ **Step 1: Database Migrations**

### **1.1 Create Base Migrations**
```bash
# Create migrations in order
php artisan make:migration add_role_to_users_table
php artisan make:migration create_activity_logs_table
php artisan make:migration create_login_attempts_table
php artisan make:migration add_email_verification_and_2fa_to_users_table
php artisan make:migration add_avatar_to_users_table
```

### **1.2 Migration Files Content**

**database/migrations/xxxx_add_role_to_users_table.php**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
```

**database/migrations/xxxx_create_activity_logs_table.php**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
```

**database/migrations/xxxx_create_login_attempts_table.php**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('successful')->default(false);
            $table->text('details')->nullable();
            $table->timestamp('attempted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
```

**database/migrations/xxxx_add_email_verification_and_2fa_to_users_table.php**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->string('verification_token')->nullable();
            $table->string('two_factor_code')->nullable();
            $table->timestamp('two_factor_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verification_token', 'two_factor_code', 'two_factor_expires_at']);
        });
    }
};
```

**database/migrations/xxxx_add_avatar_to_users_table.php**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
```

---

## 🏗️ **Step 2: Create Models**

### **2.1 Update User Model**
```bash
# Update existing User model
```

**app/Models/User.php**
```php
<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'role', 'password', 'avatar',
        'is_verified', 'verification_token', 'email_verified_at',
        'two_factor_code', 'two_factor_expires_at'
    ];

    protected $hidden = [
        'password', 'remember_token', 'verification_token', 'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    // 2FA methods
    public function generateTwoFactorCode()
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10)
        ]);
    }

    public function isTwoFactorCodeValid($code)
    {
        return $this->two_factor_code === $code &&
               $this->two_factor_expires_at &&
               now()->lt($this->two_factor_expires_at);
    }

    public function clearTwoFactorCode()
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);
    }

    // Email verification
    public function sendEmailVerificationNotification()
    {
        $this->verification_token = Str::random(64);
        $this->save();
        
        $this->notify(new \App\Notifications\EmailVerificationNotification($this));
    }

    // Relationships
    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'email', 'email');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
```

### **2.2 Create Additional Models**
```bash
php artisan make:model ActivityLog
php artisan make:model LoginAttempt
```

**app/Models/ActivityLog.php**
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'details', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**app/Models/LoginAttempt.php**
```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'email', 'ip_address', 'user_agent', 'successful',
        'details', 'attempted_at'
    ];

    protected $casts = [
        'successful' => 'boolean',
        'details' => 'array',
        'attempted_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }

    public function scopeRecent($query, $minutes = 15)
    {
        return $query->where('attempted_at', '>=', now()->subMinutes($minutes));
    }
}
```

---

## 🏗️ **Step 3: Create Middleware**

### **3.1 Create Role Middleware**
```bash
php artisan make:middleware RoleMiddleware
```

**app/Http/Middleware/RoleMiddleware.php**
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
```

### **3.2 Create Email Verification Middleware**
```bash
php artisan make:middleware EnsureEmailIsVerified
```

**app/Http/Middleware/EnsureEmailIsVerified.php**
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() ||
            (! $request->user()->hasVerifiedEmail() &&
             ! $request->routeIs('verification.*') &&
             ! $request->routeIs('logout') &&
             ! $request->routeIs('verify.email'))) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
```

### **3.3 Register Middleware**
**bootstrap/app.php**
```php
<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## 🏗️ **Step 4: Create Controllers**

### **4.1 Create Authentication Controllers**
```bash
php artisan make:controller AuthController
php artisan make:controller EmailVerificationController
php artisan make:controller TwoFactorController
php artisan make:controller PasswordResetController
php artisan make:controller LoginAttemptController
php artisan make:controller UserController --resource
php artisan make:controller UserDashboardController
```

I'll continue with the controllers in the next part. Would you like me to continue with the detailed controller implementations?
