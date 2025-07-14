<nav class="navbar navbar-expand-lg navbar-dark navbar-mdrrmo">
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
                    <!-- Incident Management -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-exclamation-triangle me-1"></i>Incidents
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('incidents.index') }}">
                                <i class="fas fa-list me-2"></i>All Incidents</a></li>
                            <li><a class="dropdown-item" href="{{ route('incidents.create') }}">
                                <i class="fas fa-plus me-2"></i>Report Incident</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('incidents.index') }}?status=pending">
                                <i class="fas fa-clock me-2"></i>Pending</a></li>
                            <li><a class="dropdown-item" href="{{ route('incidents.index') }}?status=responding">
                                <i class="fas fa-ambulance me-2"></i>Responding</a></li>
                        </ul>
                    </li>

                    <!-- Vehicle Management -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-truck me-1"></i>Vehicles
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('vehicles.index') }}">
                                <i class="fas fa-list me-2"></i>Fleet Overview</a></li>
                            @if(Auth::user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('vehicles.create') }}">
                                <i class="fas fa-plus me-2"></i>Add Vehicle</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('vehicles.index') }}?status=maintenance">
                                <i class="fas fa-wrench me-2"></i>Maintenance</a></li>
                            <li><a class="dropdown-item" href="{{ route('vehicles.index') }}?status=available">
                                <i class="fas fa-check-circle me-2"></i>Available</a></li>
                        </ul>
                    </li>

                    <!-- Analytics & Reports -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar me-1"></i>Analytics
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('heat-map.index') }}">
                                <i class="fas fa-map-marked-alt me-2"></i>Heat Map</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-file-export me-2"></i>Export Reports</a></li>
                        </ul>
                    </li>

                    @if(Auth::user()->isAdmin())
                        <!-- Admin Panel -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Admin
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
                    @endif

                    <!-- User Account -->
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
                            @if(Auth::user()->isAdmin())
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>My Dashboard</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ Auth::user()->isAdmin() ? route('admin.profile') : route('user.profile') }}">
                                <i class="fas fa-user-cog me-2"></i>My Profile</a></li>
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
