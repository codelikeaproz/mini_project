# Web App Layouts & Navigation System

## 🎯 **Overview**
This guide covers the complete layout system with navigation, themes, and responsive design for the authentication system.

## 📁 **Layout Structure**

### **Directory Structure**
```
resources/views/
├── layouts/
│   ├── app1.blade.php          # Light theme layout
│   ├── app2.blade.php          # Dark theme layout
│   └── user-app.blade.php      # User-specific layout
├── partials/
│   ├── head.blade.php          # CSS, meta tags, fonts
│   └── nav.blade.php           # Navigation component
└── auth/
    ├── login.blade.php
    ├── register.blade.php
    ├── two-factor.blade.php
    └── email-verification-notice.blade.php
```

---

## 🎨 **Step 11: Create Layout Files**

### **11.1 Create Partials Directory**
```bash
mkdir -p resources/views/partials
mkdir -p resources/views/layouts
```

### **11.2 Head Partial (CSS & Meta)**
**resources/views/partials/head.blade.php**
```php
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    :root {
        /* Modern Color Palette */
        --primary-50: #f0f4ff;
        --primary-100: #e0ecff;
        --primary-500: #1e3c72;
        --primary-600: #2a5298;
        --primary-700: #1a2f5c;
        --primary-900: #0f1a2e;

        /* Supporting Colors */
        --success: #198754;
        --warning: #fd7e14;
        --info: #0dcaf0;
        --danger: #dc3545;

        /* Neutral Palette */
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;

        /* Typography */
        --font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        --font-weight-normal: 400;
        --font-weight-medium: 500;
        --font-weight-semibold: 600;
        --font-weight-bold: 700;

        /* Spacing System */
        --space-xs: 0.25rem;
        --space-sm: 0.5rem;
        --space-md: 1rem;
        --space-lg: 1.5rem;
        --space-xl: 2rem;
        --space-2xl: 3rem;
        --space-3xl: 4rem;

        /* Radius */
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;

        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    body {
        font-family: var(--font-family);
        font-weight: var(--font-weight-normal);
        line-height: 1.6;
        color: var(--gray-700);
        background-color: var(--gray-50);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Modern Typography */
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-family);
        font-weight: var(--font-weight-semibold);
        line-height: 1.3;
        color: var(--gray-800);
        margin-bottom: var(--space-md);
    }

    /* Enhanced Card System */
    .card {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease-in-out;
        background: white;
        overflow: hidden;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .card-header {
        background: white;
        border-bottom: 1px solid var(--gray-200);
        padding: var(--space-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-800);
    }

    .card-body {
        padding: var(--space-xl);
    }

    /* Modern Button System */
    .btn {
        border-radius: var(--radius-md);
        padding: var(--space-sm) var(--space-lg);
        font-weight: var(--font-weight-medium);
        font-size: 0.875rem;
        line-height: 1.5;
        transition: all 0.15s ease-in-out;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: var(--space-xs);
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
        border-color: var(--primary-500);
        color: white;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-700) 0%, var(--primary-500) 100%);
        border-color: var(--primary-700);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }

    /* Modern Form Controls */
    .form-control {
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        padding: var(--space-sm) var(--space-md);
        font-size: 0.875rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control:focus {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
        outline: none;
    }

    .form-label {
        font-weight: var(--font-weight-medium);
        color: var(--gray-700);
        margin-bottom: var(--space-xs);
    }

    /* Modern Table */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table thead th {
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        padding: var(--space-lg);
    }

    .table tbody td {
        padding: var(--space-lg);
        border-bottom: 1px solid var(--gray-100);
    }

    .table tbody tr:hover {
        background-color: var(--gray-50);
    }

    /* Alert System */
    .alert {
        border: none;
        border-radius: var(--radius-lg);
        padding: var(--space-lg);
        border-left: 4px solid;
    }

    .alert-success {
        background: var(--success-light, #d1edda);
        border-left-color: var(--success);
        color: #0f5132;
    }

    .alert-danger {
        background: var(--danger-light, #f8d7da);
        border-left-color: var(--danger);
        color: #842029;
    }

    .alert-info {
        background: var(--info-light, #cff4fc);
        border-left-color: var(--info);
        color: #055160;
    }

    .alert-warning {
        background: var(--warning-light, #fff3cd);
        border-left-color: var(--warning);
        color: #664d03;
    }
</style>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### **11.3 Navigation Component**
**resources/views/partials/nav.blade.php**
```php
<nav class="navbar navbar-expand-lg navbar-dark bg-primary py-3">
    <div class="container-fluid px-4">
        <!-- Brand -->
        <a class="navbar-brand fw-bold fs-4" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            <i class="fas fa-cube me-3"></i>{{ config('app.name', 'AuthApp') }}
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                    @if(Auth::user()->isAdmin())
                        <!-- Admin Navigation -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Admin Panel
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('users.index') }}">
                                    <i class="fas fa-users me-2"></i>User Management</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.login-attempts') }}">
                                    <i class="fas fa-shield-alt me-2"></i>Login Attempts</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user-cog me-2"></i>My Profile</a></li>
                            </ul>
                        </li>
                    @else
                        <!-- User Navigation -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i>My Account
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                                    <i class="fas fa-user-cog me-2"></i>My Profile</a></li>
                            </ul>
                        </li>
                    @endif

                    <!-- User Info & Logout -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                     class="rounded-circle me-2" width="24" height="24">
                            @else
                                <i class="fas fa-user-circle me-1"></i>
                            @endif
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">
                                <small class="text-muted">{{ Auth::user()->isAdmin() ? 'Administrator' : 'User' }}</small>
                            </span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        z-index: 1030 !important;
        min-height: 80px;
        padding: 1rem 0 !important;
    }

    .navbar-brand {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        letter-spacing: -0.025em;
    }

    .navbar .dropdown-menu {
        z-index: 1050 !important;
        border: 0;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
        border-radius: 0.75rem;
        margin-top: 0.5rem;
        padding: 0.75rem 0;
    }

    .navbar .dropdown-item {
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
    }

    .navbar .dropdown-item:hover {
        background-color: var(--gray-100);
        transform: translateX(2px);
    }

    .navbar-nav .nav-link {
        padding: 0.75rem 1rem !important;
        border-radius: 0.5rem;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
        margin: 0 0.25rem;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }
</style>
```

### **11.4 Light Theme Layout**
**resources/views/layouts/app1.blade.php**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    @include('partials.head')
    <style>
        body {
            background: linear-gradient(135deg, var(--gray-50) 0%, #e2e8f0 100%);
            min-height: 100vh;
        }

        .main-content {
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            margin: var(--space-xl) auto;
            max-width: 1200px;
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .content-wrapper {
            padding: var(--space-2xl);
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 100px;
            right: var(--space-lg);
            z-index: 1060;
            max-width: 400px;
        }

        .toast {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            margin-bottom: var(--space-sm);
        }

        .toast-success {
            background: white;
            border-left: 4px solid var(--success);
        }

        .toast-error {
            background: white;
            border-left: 4px solid var(--danger);
        }

        .toast-warning {
            background: white;
            border-left: 4px solid var(--warning);
        }

        .toast-info {
            background: white;
            border-left: 4px solid var(--info);
        }

        .toast-header {
            background: transparent;
            border-bottom: 1px solid var(--gray-100);
            padding: var(--space-lg);
            font-weight: var(--font-weight-semibold);
        }

        .toast-body {
            padding: var(--space-md) var(--space-lg) var(--space-lg);
            font-weight: var(--font-weight-medium);
            color: var(--gray-700);
        }

        /* Footer */
        .footer {
            background: transparent;
            text-align: center;
            margin-top: var(--space-3xl);
            padding: var(--space-xl) 0;
        }

        .footer-content {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: var(--font-weight-medium);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .main-content {
                margin: var(--space-lg) var(--space-md);
                border-radius: var(--radius-lg);
            }

            .content-wrapper {
                padding: var(--space-lg);
            }

            .toast-container {
                top: 80px;
                right: var(--space-md);
                left: var(--space-md);
                max-width: none;
            }
        }
    </style>
</head>
<body>
    @include('partials.nav')

    <!-- Toast Container -->
    <div class="toast-container">
        @if(session('success'))
        <div class="toast toast-success show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fas fa-check-circle me-2 text-success"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('success') }}</div>
        </div>
        @endif

        @if(session('error'))
        <div class="toast toast-error show" role="alert" data-bs-autohide="true" data-bs-delay="6000">
            <div class="toast-header">
                <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('error') }}</div>
        </div>
        @endif

        @if(session('warning'))
        <div class="toast toast-warning show" role="alert" data-bs-autohide="true" data-bs-delay="5000">
            <div class="toast-header">
                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                <strong class="me-auto">Warning</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('warning') }}</div>
        </div>
        @endif

        @if(session('info'))
        <div class="toast toast-info show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="toast-header">
                <i class="fas fa-info-circle me-2 text-info"></i>
                <strong class="me-auto">Information</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('info') }}</div>
        </div>
        @endif

        <!-- Authentication specific toasts -->
        @if(session('login_success'))
        <div class="toast toast-success show" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="toast-header">
                <i class="fas fa-sign-in-alt me-2 text-success"></i>
                <strong class="me-auto">Welcome</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('login_success') }}</div>
        </div>
        @endif

        @if(session('logout_success'))
        <div class="toast toast-info show" role="alert" data-bs-autohide="true" data-bs-delay="3000">
            <div class="toast-header">
                <i class="fas fa-sign-out-alt me-2 text-info"></i>
                <strong class="me-auto">Goodbye</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('logout_success') }}</div>
        </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <script>
        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });
        });
    </script>
</body>
</html>
```

### **11.5 Dark Theme Layout**
**resources/views/layouts/app2.blade.php**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    @include('partials.head')
    <style>
        /* Dark Theme Overrides */
        body {
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-900) 100%);
            color: var(--gray-100);
            min-height: 100vh;
        }

        .main-content {
            background: var(--gray-800);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            margin: var(--space-xl) auto;
            max-width: 1200px;
            overflow: hidden;
            border: 1px solid var(--gray-700);
        }

        .content-wrapper {
            padding: var(--space-2xl);
        }

        /* Dark theme overrides */
        .card {
            background: var(--gray-800);
            border-color: var(--gray-700);
            color: var(--gray-100);
        }

        .card-header {
            background: var(--gray-700);
            border-bottom-color: var(--gray-600);
            color: var(--gray-100);
        }

        .form-control {
            background: var(--gray-700);
            border-color: var(--gray-600);
            color: var(--gray-100);
        }

        .form-control:focus {
            background: var(--gray-700);
            border-color: var(--primary-500);
            color: var(--gray-100);
        }

        .form-label {
            color: var(--gray-200);
        }

        .table {
            background: var(--gray-800);
            color: var(--gray-100);
        }

        .table thead th {
            background: var(--gray-700);
            color: var(--gray-200);
        }

        .table tbody tr:hover {
            background-color: var(--gray-700);
        }

        /* Dark theme toasts */
        .toast {
            background: var(--gray-800);
            color: var(--gray-100);
        }

        .toast-header {
            border-bottom-color: var(--gray-700);
            color: var(--gray-100);
        }

        .toast-body {
            color: var(--gray-200);
        }

        /* Footer */
        .footer {
            border-top: 1px solid var(--gray-700);
            margin-top: var(--space-3xl);
            padding: var(--space-xl) 0;
        }

        .footer-content {
            color: var(--gray-400);
        }
    </style>
</head>
<body>
    @include('partials.nav')

    <!-- Toast Container (same as light theme) -->
    <div class="toast-container">
        <!-- Same toast structure as app1.blade.php -->
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
            </div>
        </div>
    </footer>

    <script>
        // Auto-hide toasts
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });
        });
    </script>
</body>
</html>
```

### **11.6 User-Specific Layout**
**resources/views/layouts/user-app.blade.php**
```php
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }} - User Dashboard</title>
    @include('partials.head')
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            margin: 2rem auto;
            max-width: 1200px;
            padding: 2rem;
        }

        .navbar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%) !important;
        }

        /* User-specific styling */
        .user-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .user-card h1 {
            color: white;
            margin-bottom: 0.5rem;
        }

        .user-card p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
    </style>
</head>
<body>
    @include('partials.nav')

    <!-- User Welcome Card -->
    <div class="container mt-4">
        <div class="user-card">
            <h1>Welcome, {{ auth()->user()->name }}!</h1>
            <p>{{ auth()->user()->isAdmin() ? 'Administrator Dashboard' : 'User Dashboard' }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-4">
        <div class="container">
            <span class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
        </div>
    </footer>
</body>
</html>
```

---

## 🎯 **Step 12: Update View Files to Use Layouts**

### **12.1 Update Authentication Views**

**resources/views/auth/login.blade.php**
```php
@extends('layouts.app1')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Login</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', session('verified_email')) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                    <span class="mx-2">•</span>
                    <a href="{{ route('register') }}" class="text-decoration-none">Create Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

**resources/views/auth/register.blade.php**
```php
@extends('layouts.app1')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Create Account</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none">Already have an account? Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🎯 **Step 13: Dashboard Views**

### **13.1 Admin Dashboard**
**resources/views/admin/dashboard.blade.php**
```php
@extends('layouts.app1')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-tachometer-alt"></i>Admin Dashboard
            </h1>
            <p class="text-muted">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h5 class="card-title">User Management</h5>
                <p class="card-text">Manage system users and roles</p>
                <a href="{{ route('users.index') }}" class="btn btn-primary">
                    <i class="fas fa-users me-2"></i>Manage Users
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                <h5 class="card-title">Login Attempts</h5>
                <p class="card-text">Monitor security and access</p>
                <a href="{{ route('admin.login-attempts') }}" class="btn btn-success">
                    <i class="fas fa-shield-alt me-2"></i>View Attempts
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-user-cog fa-3x text-info mb-3"></i>
                <h5 class="card-title">My Profile</h5>
                <p class="card-text">Update your admin profile</p>
                <a href="{{ route('admin.profile') }}" class="btn btn-info">
                    <i class="fas fa-user-cog me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-chart-bar fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Statistics</h5>
                <p class="card-text">View system analytics</p>
                <a href="#" class="btn btn-warning">
                    <i class="fas fa-chart-bar me-2"></i>View Stats
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
```

### **13.2 User Dashboard**
**resources/views/user/dashboard.blade.php**
```php
@extends('layouts.user-app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-user-cog fa-3x text-primary mb-3"></i>
                <h5 class="card-title">My Profile</h5>
                <p class="card-text">Update your personal information and settings</p>
                <a href="{{ route('user.profile') }}" class="btn btn-primary">
                    <i class="fas fa-user-cog me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-history fa-3x text-info mb-3"></i>
                <h5 class="card-title">Recent Activity</h5>
                <p class="card-text">View your recent account activity</p>
                <a href="#" class="btn btn-info">
                    <i class="fas fa-history me-2"></i>View Activity
                </a>
            </div>
        </div>
    </div>
</div>

@if(isset($recentActivities) && $recentActivities->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Activities</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Date</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivities as $activity)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</span>
                                </td>
                                <td>{{ $activity->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $activity->ip_address }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
```

This comprehensive layout system provides:

✅ **Responsive Navigation** with role-based menus  
✅ **Multiple Theme Support** (Light/Dark/User themes)  
✅ **Toast Notification System** for all feedback  
✅ **Modern CSS Framework** with custom variables  
✅ **Mobile-First Design** with responsive breakpoints  
✅ **Authentication-Aware Navigation** showing different options for guests/users/admins  
✅ **Consistent Styling** across all components  
✅ **Avatar Support** in navigation  
✅ **Professional Footer** with copyright  

The layouts are now ready to use with your authentication system! 
