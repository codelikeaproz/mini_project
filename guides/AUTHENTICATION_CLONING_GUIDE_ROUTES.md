# Complete Routes System for Authentication

## 🎯 **Overview**
This guide covers the complete routing structure for the authentication system with role-based access control, including all URL patterns and middleware protection.

## 📁 **Routes Structure**

### **Complete routes/web.php File**
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\LoginAttemptController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| These routes handle user authentication: login, register, logout
*/

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Main dashboard route (protected by auth and verified middleware)
Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard')
    ->middleware(['auth', 'verified']);

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
| These routes handle password reset functionality
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
| These routes handle email verification for new users
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
| These routes handle 2FA verification process
*/

Route::get('/2fa/verify', [TwoFactorController::class, 'showVerifyForm'])
    ->name('2fa.verify.form');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])
    ->name('2fa.verify');
Route::post('/2fa/resend', [TwoFactorController::class, 'resendCode'])
    ->name('2fa.resend');
Route::get('/2fa/refresh-token', [TwoFactorController::class, 'refreshToken'])
    ->name('2fa.refresh.token');

/*
|--------------------------------------------------------------------------
| Theme/Layout Switcher Route
|--------------------------------------------------------------------------
| Allows users to switch between different themes
*/

Route::get('switch-layout/{layout}', function($layout) {
    session(['layout' => $layout]);
    return back();
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Product Management
|--------------------------------------------------------------------------
| These routes are protected by: auth, verified, role:admin middleware
| Only administrators can access these routes
*/

Route::prefix('products')->name('products.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Export routes (must be defined before resource routes)
    Route::get('export/csv', [ProductController::class, 'exportCSV'])->name('export.csv');
    Route::get('export/excel', [ProductController::class, 'exportExcel'])->name('export.excel');
    Route::get('export/pdf', [ProductController::class, 'exportPDF'])->name('export.pdf');
    Route::get('logs', [ProductController::class, 'logs'])->name('logs');

    // Standard CRUD routes for products
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('{product}', [ProductController::class, 'show'])->name('show');
    Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - User Management
|--------------------------------------------------------------------------
| These routes handle user CRUD operations for administrators
| Protected by: auth, verified, role:admin middleware
*/

Route::prefix('users')->name('users.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Profile & Security Management
|--------------------------------------------------------------------------
| Admin-specific profile management and security monitoring
| Protected by: auth, verified, role:admin middleware
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/profile', [UserController::class, 'adminProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateAdminProfile'])->name('profile.update');
    Route::get('/login-attempts', [LoginAttemptController::class, 'index'])->name('login-attempts');
});

/*
|--------------------------------------------------------------------------
| User Routes - Personal Dashboard & Management
|--------------------------------------------------------------------------
| These routes are for regular users (non-admin)
| Protected by: auth, verified, role:user middleware
*/

Route::prefix('user')->name('user.')->middleware(['auth', 'verified', 'role:user'])->group(function () {
    // User dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('dashboard');
    
    // User's personal products management
    Route::get('/products', [UserDashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [UserDashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [UserDashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}', [UserDashboardController::class, 'showProduct'])->name('products.show');
    Route::get('/products/{product}/edit', [UserDashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [UserDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [UserDashboardController::class, 'destroyProduct'])->name('products.destroy');
    
    // User profile management
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Development/Testing Routes
|--------------------------------------------------------------------------
| These routes are for testing email functionality
| REMOVE THESE IN PRODUCTION!
*/

// Test email route (remove in production)
Route::get('/test-email', function () {
    try {
        $testEmail = 'your-email@example.com';
        Mail::to($testEmail)->send(new \App\Mail\TestEmail());
        return 'Test email sent successfully to ' . $testEmail . '! Check your inbox.';
    } catch (Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
})->name('test.email');

// Test OTP email route (remove in production)
Route::get('/test-otp', function () {
    try {
        $testEmail = 'your-email@example.com';
        $user = \App\Models\User::where('email', $testEmail)->first();

        if (!$user) {
            return 'User not found with email: ' . $testEmail;
        }

        // Generate OTP
        $user->generateTwoFactorCode();

        // Send OTP email
        Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($user));

        return 'OTP email sent successfully to ' . $testEmail . '! Generated code: ' . $user->two_factor_code . ' (Expires: ' . $user->two_factor_expires_at . ')';
    } catch (Exception $e) {
        return 'Error sending OTP email: ' . $e->getMessage();
    }
})->name('test.otp');

/*
|--------------------------------------------------------------------------
| Root Route
|--------------------------------------------------------------------------
| Redirects users to appropriate page based on authentication status
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});
```

---

## 🔐 **Route Protection Middleware**

### **Middleware Combinations Used:**

1. **`auth`** - Requires user to be logged in
2. **`verified`** - Requires email to be verified
3. **`role:admin`** - Requires user to have admin role
4. **`role:user`** - Requires user to have user role

### **Protection Levels:**

```php
// No protection (public routes)
Route::get('/login', [...]);
Route::get('/register', [...]);

// Basic authentication required
Route::middleware(['auth'])->group(function () {
    // Routes that need login only
});

// Authentication + Email verification required
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [...]);
});

// Admin-only routes (auth + verified + admin role)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Admin routes
});

// User-only routes (auth + verified + user role)
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    // User routes
});
```

---

## 🎯 **Route Groups Breakdown**

### **1. Public Routes (No Authentication)**
```php
/login (GET, POST)           # Login form and processing
/register (GET, POST)        # Registration form and processing
/forgot-password (GET, POST) # Password reset request
/reset-password/{token}      # Password reset form
/verify-email/{token}        # Email verification
/email/verify               # Email verification notice
/2fa/verify (GET, POST)     # Two-factor authentication
```

### **2. Authenticated Routes (auth + verified)**
```php
/dashboard                  # Main dashboard (redirects based on role)
/switch-layout/{layout}     # Theme switcher
```

### **3. Admin Routes (auth + verified + role:admin)**
```php
# Product Management
/products                   # List all products
/products/create           # Create product form
/products/{id}             # View product
/products/{id}/edit        # Edit product form
/products/export/pdf       # Export products to PDF
/products/logs             # Activity logs

# User Management
/users                     # List all users
/users/create             # Create user form
/users/{id}               # View user
/users/{id}/edit          # Edit user form

# Admin Profile & Security
/admin/profile            # Admin profile management
/admin/login-attempts     # View login attempts
```

### **4. User Routes (auth + verified + role:user)**
```php
# User Dashboard
/user/dashboard           # User's personal dashboard

# User's Products
/user/products           # User's own products
/user/products/create    # Create personal product
/user/products/{id}      # View own product
/user/products/{id}/edit # Edit own product

# User Profile
/user/profile            # User profile management
```

---

## 🛣️ **Named Routes Reference**

### **Authentication Routes**
```php
route('login')                    # /login
route('register')                 # /register
route('logout')                   # /logout
route('dashboard')                # /dashboard
route('password.request')         # /forgot-password
route('password.reset', $token)   # /reset-password/{token}
route('verify.email', $token)     # /verify-email/{token}
route('2fa.verify.form')          # /2fa/verify
```

### **Admin Routes**
```php
route('products.index')           # /products
route('products.create')          # /products/create
route('products.show', $id)       # /products/{id}
route('products.edit', $id)       # /products/{id}/edit
route('products.export.pdf')      # /products/export/pdf
route('products.logs')            # /products/logs

route('users.index')              # /users
route('users.create')             # /users/create
route('users.show', $id)          # /users/{id}
route('users.edit', $id)          # /users/{id}/edit

route('admin.profile')            # /admin/profile
route('admin.login-attempts')     # /admin/login-attempts
```

### **User Routes**
```php
route('user.dashboard')           # /user/dashboard
route('user.products')            # /user/products
route('user.products.create')     # /user/products/create
route('user.products.show', $id)  # /user/products/{id}
route('user.profile')             # /user/profile
```

---

## 🔧 **Route Parameters**

### **Dynamic Parameters Used:**
```php
{token}     # Password reset token, email verification token
{product}   # Product ID (model binding)
{user}      # User ID (model binding)
{layout}    # Layout name for theme switching
```

### **Model Binding:**
Laravel automatically resolves `{product}` and `{user}` to model instances:
```php
// This automatically finds Product by ID
Route::get('/products/{product}', [ProductController::class, 'show']);

// This automatically finds User by ID
Route::get('/users/{user}', [UserController::class, 'show']);
```

---

## 🎨 **URL Structure Examples**

### **Admin URLs:**
```
https://yourapp.com/products                    # Product list
https://yourapp.com/products/create             # Create product
https://yourapp.com/products/123                # View product #123
https://yourapp.com/products/123/edit           # Edit product #123
https://yourapp.com/products/export/pdf         # Export PDF
https://yourapp.com/products/logs               # Activity logs

https://yourapp.com/users                       # User list
https://yourapp.com/users/create                # Create user
https://yourapp.com/users/456                   # View user #456
https://yourapp.com/users/456/edit              # Edit user #456

https://yourapp.com/admin/profile               # Admin profile
https://yourapp.com/admin/login-attempts        # Login attempts
```

### **User URLs:**
```
https://yourapp.com/user/dashboard               # User dashboard
https://yourapp.com/user/products                # User's products
https://yourapp.com/user/products/create         # Create personal product
https://yourapp.com/user/products/789            # View own product #789
https://yourapp.com/user/profile                 # User profile
```

### **Authentication URLs:**
```
https://yourapp.com/login                        # Login page
https://yourapp.com/register                     # Registration
https://yourapp.com/forgot-password              # Password reset request
https://yourapp.com/reset-password/abc123        # Password reset form
https://yourapp.com/verify-email/def456          # Email verification
https://yourapp.com/2fa/verify                   # 2FA verification
```

---

## 🚀 **Route Testing Commands**

### **List All Routes:**
```bash
php artisan route:list
```

### **Filter Routes by Name:**
```bash
php artisan route:list --name=admin
php artisan route:list --name=user
php artisan route:list --name=auth
```

### **Filter Routes by Middleware:**
```bash
php artisan route:list --middleware=auth
php artisan route:list --middleware=role
```

---

## 🔒 **Security Features in Routes**

1. **CSRF Protection**: All POST/PUT/DELETE routes automatically protected
2. **Role-Based Access**: Different route groups for admin/user roles
3. **Email Verification**: Required for accessing protected routes
4. **Authentication**: Login required for sensitive operations
5. **Model Binding**: Automatic model resolution with security checks
6. **Middleware Stacking**: Multiple middleware layers for enhanced security

This routing system provides a complete, secure, and organized structure for your authentication system with role-based access control! 
