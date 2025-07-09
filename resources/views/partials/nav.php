<nav class="navbar navbar-expand-lg navbar-dark bg-success py-3">
    <div class="container-fluid px-4">
        <!-- MDRRMO Brand -->
        <a class="navbar-brand fw-bold fs-4" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
            <i class="fas fa-shield-alt me-3"></i>MDRRMO Maramag
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
                                    <i class="fas fa-users me-2"></i>Staff Management</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.login-attempts') }}">
                                    <i class="fas fa-shield-alt me-2"></i>Security Logs</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="fas fa-user-cog me-2"></i>Admin Profile</a></li>
                            </ul>
                        </li>
                    @else
                        <!-- MDRRMO Staff Navigation -->
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
                            {{ Auth::user()->full_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">
                                <small class="text-muted">{{ Auth::user()->position ?? Auth::user()->role }}</small><br>
                                <small class="text-muted">{{ Auth::user()->municipality }}</small>
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
                            <i class="fas fa-sign-in-alt me-1"></i>Staff Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<style>
    .bg-success {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
    }

    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        background-color: #f8f9fa;
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