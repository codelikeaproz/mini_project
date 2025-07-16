<!-- MDRRMO Navigation - Professional & Clean Design -->
<nav class="navbar bg-gradient-to-r from-slate-600 to-slate-700 shadow-lg border-b border-slate-800/20" role="navigation" aria-label="MDRRMO Main Navigation">
    <div class="navbar-start">
        <!-- Mobile Hamburger Menu -->
        <div class="dropdown lg:hidden">
            <div tabindex="0" role="button" class="btn btn-ghost text-white hover:bg-white/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </div>
            <!-- Mobile Dropdown Menu -->
            <ul tabindex="0" class="menu dropdown-content bg-white rounded-lg shadow-xl border border-slate-200 z-50 w-80 p-4 mt-2">
                @auth
                    <!-- Mobile User Header -->
                    <li class="mb-4">
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="bg-slate-600 text-white rounded-full w-12 h-12 flex items-center justify-center">
                                        <span class="text-lg font-semibold">{{ substr(Auth::user()->full_name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ Auth::user()->full_name }}</div>
                                    <div class="text-sm text-slate-500">{{ Auth::user()->position ?? 'MDRRMO Staff' }}</div>
                                    <div class="text-xs text-slate-400">{{ Auth::user()->municipality }}</div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Core Navigation -->
                    <li class="menu-title text-slate-600 font-semibold">
                        <span>Emergency Operations</span>
                    </li>
                    <li><a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-home text-slate-500"></i>Dashboard</a></li>
                    <li><a href="{{ route('incidents.create') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-plus-circle text-green-600"></i>Report Incident</a></li>
                    <li><a href="{{ route('incidents.index') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-list text-slate-500"></i>All Incidents</a></li>
                    <li><a href="{{ route('vehicles.index') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-ambulance text-blue-600"></i>Fleet Management</a></li>
                    <li><a href="{{ route('heat-map.index') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-map-marked-alt text-red-500"></i>Heat Map Analytics</a></li>

                    @if(Auth::user()->isAdmin())
                        <div class="divider my-2"></div>
                        <li class="menu-title text-slate-600 font-semibold">
                            <span>Administration</span>
                        </li>
                        <li><a href="{{ route('users.index') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-users text-purple-600"></i>Staff Management</a></li>
                        <li><a href="{{ route('victims.index') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-user-injured text-orange-600"></i>Victim Records</a></li>
                        <li><a href="{{ route('admin.login-attempts') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-shield-alt text-yellow-600"></i>Security Logs</a></li>
                    @endif

                    <div class="divider my-2"></div>
                    <!-- Account Actions -->
                    <li><a href="{{ Auth::user()->isAdmin() ? route('admin.profile') : route('user.profile') }}"
                          class="text-slate-700 hover:bg-slate-100 rounded-lg">
                        <i class="fas fa-user-cog text-slate-500"></i>Profile Settings</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 rounded-lg p-2 flex items-center gap-3">
                                <i class="fas fa-sign-out-alt"></i>Sign Out
                            </button>
                        </form>
                    </li>
                @else
                    <li class="text-center">
                        <a href="{{ route('login') }}" class="btn btn-primary w-full">
                            <i class="fas fa-sign-in-alt"></i>Staff Login
                        </a>
                    </li>
                @endauth
            </ul>
        </div>

        <!-- MDRRMO Brand Logo -->
        <div class="flex items-center">
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
               class="btn btn-ghost text-white hover:bg-white/10 text-xl font-bold normal-case">
                <div class="flex items-center gap-3">
                    <i class="fas fa-shield-alt text-2xl text-green-400"></i>
                    <div class="hidden sm:block">
                        <div class="text-lg font-bold">MDRRMO</div>
                        <div class="text-xs opacity-80 -mt-1">Maramag</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Desktop Navigation -->
    <div class="navbar-end hidden lg:flex">
        <div class="flex items-center gap-2">
            @auth
                <!-- Primary Action Button -->
                <a href="{{ route('incidents.create') }}"
                   class="btn btn-success btn-sm gap-2">
                    <i class="fas fa-plus"></i>
                    Report Incident
                </a>

                <!-- Quick Access Dropdown -->
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm text-white hover:bg-white/10">
                        <i class="fas fa-th-large text-lg"></i>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-white shadow-xl rounded-lg border border-slate-200 z-50 w-72 p-3 mt-2">
                        <li class="menu-title text-slate-600 font-semibold mb-2">
                            <span>Quick Access</span>
                        </li>
                        <li><a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-home text-slate-500 w-5"></i>Dashboard</a></li>
                        <li><a href="{{ route('incidents.index') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-list text-slate-500 w-5"></i>All Incidents</a></li>
                        <li><a href="{{ route('vehicles.index') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-ambulance text-blue-600 w-5"></i>Fleet Status</a></li>
                        <li><a href="{{ route('heat-map.index') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-map-marked-alt text-red-500 w-5"></i>Analytics</a></li>

                        @if(Auth::user()->isAdmin())
                            <div class="divider my-1"></div>
                            <li class="menu-title text-slate-600 font-semibold">
                                <span>Administration</span>
                            </li>
                            <li><a href="{{ route('users.index') }}"
                                  class="text-slate-700 hover:bg-slate-100 rounded-lg">
                                <i class="fas fa-users text-purple-600 w-5"></i>Staff Management</a></li>
                            <li><a href="{{ route('victims.index') }}"
                                  class="text-slate-700 hover:bg-slate-100 rounded-lg">
                                <i class="fas fa-user-injured text-orange-600 w-5"></i>Victim Records</a></li>
                            <li><a href="{{ route('admin.login-attempts') }}"
                                  class="text-slate-700 hover:bg-slate-100 rounded-lg">
                                <i class="fas fa-shield-alt text-yellow-600 w-5"></i>Security Logs</a></li>
                        @endif
                    </ul>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle text-white hover:bg-white/10">
                        <div class="avatar placeholder">
                            <div class="bg-white/20 text-white rounded-full w-8 h-8 flex items-center justify-center">
                                <span class="text-sm font-medium">{{ substr(Auth::user()->full_name, 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-white shadow-xl rounded-lg border border-slate-200 z-50 w-80 p-4 mt-2">
                        <!-- User Info Header -->
                        <li class="mb-3">
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 pointer-events-none">
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-slate-600 text-white rounded-full w-12 h-12 flex items-center justify-center">
                                            <span class="text-lg font-semibold">{{ substr(Auth::user()->full_name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ Auth::user()->full_name }}</div>
                                        <div class="text-sm text-slate-500">{{ Auth::user()->position ?? 'MDRRMO Staff' }}</div>
                                        <div class="text-xs text-slate-400">{{ Auth::user()->municipality }}</div>
                                        @if(Auth::user()->isAdmin())
                                            <div class="badge badge-primary badge-sm mt-1">Administrator</div>
                                        @else
                                            <div class="badge badge-secondary badge-sm mt-1">Staff</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li><a href="{{ Auth::user()->isAdmin() ? route('admin.profile') : route('user.profile') }}"
                              class="text-slate-700 hover:bg-slate-100 rounded-lg">
                            <i class="fas fa-user-cog text-slate-500 w-5"></i>Profile Settings</a></li>

                        <div class="divider my-1"></div>

                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full text-left text-red-600 hover:bg-red-50 rounded-lg p-2 flex items-center gap-3">
                                    <i class="fas fa-sign-out-alt w-5"></i>Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <!-- Guest Login Button -->
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Staff Login
                </a>
            @endauth
        </div>
    </div>
</nav>
