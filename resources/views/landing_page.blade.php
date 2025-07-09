<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>MDRRMO Accident Reporting System - Bukidnon</title>
</head>
<body class="bg-base-100">
    <!-- Navigation -->
    <nav class="navbar bg-white/95 backdrop-blur-sm shadow-sm border-b border-base-200 sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="navbar-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-focus rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-xl font-bold text-white">M</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">MDRRMO</h1>
                        <p class="text-sm text-gray-500 font-medium">Bukidnon</p>
                    </div>
                </div>
            </div>
            <div class="navbar-end">
                <a href="#" class="btn btn-primary btn-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Staff Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-[85vh] flex items-center">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-8">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary font-semibold rounded-full text-sm mb-6">
                        Emergency Response System
                    </span>
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-[1.1]">
                        Emergency Response
                        <span class="bg-gradient-to-r from-primary to-primary-focus bg-clip-text text-transparent block">
                            Made Digital
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed font-light">
                        Streamlining accident reporting and emergency coordination for
                        <span class="font-semibold text-primary">Maramag, Bukidnon</span>
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="#" class="btn btn-primary btn-lg rounded-xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Access System
                    </a>
                    <a href="#features" class="btn btn-outline btn-lg rounded-xl border-2 hover:bg-primary hover:border-primary transition-all duration-300">
                        Learn More
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative">
        <div class="container mx-auto px-4">
            <!-- Section Header -->
            <div class="text-center mb-20">
                <div class="max-w-3xl mx-auto">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary font-semibold rounded-full text-sm mb-4">
                        Key Features
                    </span>
                    <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                        Modern Emergency Management
                    </h2>
                    <p class="text-xl text-gray-600 leading-relaxed">
                        Transforming traditional paper-based processes into efficient digital workflows
                    </p>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <!-- Feature 1 -->
                <div class="group">
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-500 p-8 border border-gray-100 hover:border-primary/20 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-focus rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Digital Reporting</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Comprehensive incident reporting for 8 emergency types with real-time data capture and automated workflows.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="group">
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-500 p-8 border border-gray-100 hover:border-primary/20 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-focus rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Fleet Management</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Real-time vehicle tracking, fuel monitoring, and personnel deployment coordination for optimal response times.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="group">
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-500 p-8 border border-gray-100 hover:border-primary/20 transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-focus rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Analytics Dashboard</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Interactive charts and insights for data-driven emergency response planning and performance monitoring.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-24 bg-gradient-to-br from-primary to-primary-focus relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-white/20 text-white font-semibold rounded-full text-sm mb-4">
                    Impact Metrics
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Transforming Emergency Response
                </h2>
                <p class="text-xl text-white/90 max-w-2xl mx-auto leading-relaxed">
                    Measurable improvements in emergency response efficiency and coordination
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-center border border-white/20 hover:bg-white/15 transition-all duration-300">
                    <div class="text-5xl md:text-6xl font-bold text-white mb-2">90%</div>
                    <div class="text-xl font-semibold text-white mb-2">Faster Retrieval</div>
                    <div class="text-white/80">18 min → 2 min</div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-center border border-white/20 hover:bg-white/15 transition-all duration-300">
                    <div class="text-5xl md:text-6xl font-bold text-white mb-2">8</div>
                    <div class="text-xl font-semibold text-white mb-2">Emergency Types</div>
                    <div class="text-white/80">Comprehensive coverage</div>
                </div>

                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-center border border-white/20 hover:bg-white/15 transition-all duration-300">
                    <div class="text-5xl md:text-6xl font-bold text-white mb-2">100%</div>
                    <div class="text-xl font-semibold text-white mb-2">Digital</div>
                    <div class="text-white/80">Paperless operations</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Alert -->
    <section class="bg-gradient-to-r from-red-500 to-red-600 text-white py-6">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-4 text-center">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 3h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <span class="font-medium text-lg">
                    For immediate emergencies, please call <strong class="font-bold text-xl">911</strong> or contact your local emergency services
                </span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200">
        <div class="container mx-auto px-4 py-12">
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-primary to-primary-focus rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl font-bold text-white">M</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    Municipal Disaster Risk Reduction & Management Office
                </h3>
                <p class="text-lg text-gray-600 mb-6">Maramag, Bukidnon</p>
                <div class="w-20 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent mx-auto mb-6"></div>
                <p class="text-sm text-gray-500">
                    © 2025 MDRRMO Maramag. Emergency Response System. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <style>
        .bg-grid-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.1'%3E%3Ccircle cx='5' cy='5' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</body>
</html>