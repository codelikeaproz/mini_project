<!DOCTYPE html>
<html lang="en" data-theme="mdrrmo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MDRRMO Maramag - Accident Reporting System')</title>

            <!-- Keep Bootstrap CSS for existing views compatibility -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Vite Assets (Tailwind + DaisyUI) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS fixes for Bootstrap + DaisyUI compatibility -->
    <style>
        /* Fix navbar positioning issues */
        body {
            padding-top: 0;
            margin-top: 0;
        }

        .navbar {
            position: relative !important;
            z-index: 1030;
            margin-bottom: 0;
        }

        /* Ensure content doesn't overlap with navbar */
        .main-content {
            margin-top: 0;
            padding-top: 0;
        }

        /* Ensure DaisyUI badges work in victim management */
        .victim-management .badge {
            /* Reset Bootstrap badge styles for this section */
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* DaisyUI badge colors */
        .victim-management .badge-neutral { background-color: #6b7280; color: white; }
        .victim-management .badge-success { background-color: #22c55e; color: white; }
        .victim-management .badge-warning { background-color: #f59e0b; color: white; }
        .victim-management .badge-error { background-color: #ef4444; color: white; }
        .victim-management .badge-info { background-color: #3b82f6; color: white; }
        .victim-management .badge-ghost { background-color: #f3f4f6; color: #374151; }

        /* DaisyUI navbar and dropdowns work automatically */
        .navbar {
            z-index: 9999 !important;
            position: relative !important;
        }

        /* Ensure DaisyUI dropdowns have proper z-index */
        .dropdown-content {
            z-index: 10000 !important;
        }

        /* Fix map container z-index to not interfere with navbar */
        .map-container, #map, #heatMap {
            z-index: 1 !important;
            position: relative !important;
        }

        /* Leaflet map specific z-index fixes */
        .leaflet-container {
            z-index: 1 !important;
            position: relative !important;
        }

        .leaflet-pane {
            z-index: auto !important;
        }

        /* Ensure all leaflet controls stay below navbar */
        .leaflet-control-container {
            z-index: 100 !important;
        }

                        /* Fix Bootstrap modal z-index to appear above navbar and dropdowns */
        .modal {
            z-index: 11000 !important;
        }

        .modal.show {
            display: block !important;
            opacity: 1 !important;
        }

        .modal-backdrop {
            z-index: 10500 !important;
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }
    </style>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js (for analytics) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- MDRRMO Custom Styles - Minimal & Clean -->
    <style>
        /* Font Family Override */
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        /* Navigation Enhancements */
        .navbar {
            min-height: 70px;
        }

        /* Dropdown Menu Enhancements */
        .dropdown-content {
            border: 1px solid hsl(var(--b3));
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Smooth Transitions */
        .menu li > a:hover,
        .btn {
            transition: all 0.2s ease;
        }

        /* Bootstrap Compatibility Styles */
        .page-header {
            background: linear-gradient(135deg, hsl(var(--b2)) 0%, hsl(var(--b3)) 100%);
            border-bottom: 1px solid hsl(var(--b3));
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .page-title {
            color: hsl(var(--bc));
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: hsl(var(--bc) / 0.7);
            font-weight: 400;
        }

        /* Content Area */
        .main-content {
            min-height: calc(100vh - 160px);
            padding: 0;
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            color: #6c757d;
            /* text-align: center; */
            width: 100%;
            padding: 1.5rem 0;
            margin: 0 auto;
            border-top: 1px solid #e9ecef;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    @include('partials.nav')

    <!-- Page Header -->
    @hasSection('page-header')
        <div class="page-header">
            <div class="container">
                @yield('page-header')
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0 text-center" >&copy; {{ date('Y') }} MDRRMO Maramag. All rights reserved. | Accident Reporting & Vehicle Utilization System</p>
    </footer>

    <!-- Bootstrap JS (kept for other views like alerts, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JavaScript -->
    <script>
        // Wait for DOM and Bootstrap to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (alert.querySelector('.btn-close')) {
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    }
                });
            }, 5000);

            // Add fade-in animation to main content
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }

            // DaisyUI dropdowns work automatically, no JavaScript initialization needed
            console.log('DaisyUI navigation loaded successfully');
        });

        // ========================================
        // MDRRMO REUSABLE ALERT SYSTEM
        // ========================================

        /**
         * Show success toast notification
         * @param {string} message - Success message to display
         * @param {number} timer - Auto-close timer in milliseconds (default: 3000)
         */
        function showSuccessToast(message, timer = 3000) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                background: '#f8faf9',
                color: '#2f3833',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'success',
                title: message,
                iconColor: '#556159'
            });
        }

        /**
         * Show error toast notification
         * @param {string} message - Error message to display
         * @param {number} timer - Auto-close timer in milliseconds (default: 5000)
         */
        function showErrorToast(message, timer = 5000) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                background: '#faf8f8',
                color: '#2f3833',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'error',
                title: message,
                iconColor: '#7a5a5a'
            });
        }

        /**
         * Show warning toast notification
         * @param {string} message - Warning message to display
         * @param {number} timer - Auto-close timer in milliseconds (default: 4000)
         */
        function showWarningToast(message, timer = 4000) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                background: '#faf9f8',
                color: '#2f3833',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'warning',
                title: message,
                iconColor: '#8b7355'
            });
        }

        /**
         * Show info toast notification
         * @param {string} message - Info message to display
         * @param {number} timer - Auto-close timer in milliseconds (default: 3000)
         */
        function showInfoToast(message, timer = 3000) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: timer,
                timerProgressBar: true,
                background: '#f8faf9',
                color: '#2f3833',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'info',
                title: message,
                iconColor: '#6b7671'
            });
        }

        /**
         * Show reusable delete confirmation dialog
         * @param {string} title - Dialog title
         * @param {string} text - Dialog text/description
         * @param {string} itemName - Name of item being deleted
         * @param {string} confirmButtonText - Text for confirm button
         * @param {function} onConfirm - Callback function when confirmed
         */
        function showDeleteConfirmation(title, text, itemName, confirmButtonText, onConfirm) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#7a5a5a',
                cancelButtonColor: '#6b7671',
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                background: '#fafbfb',
                color: '#2f3833',
                customClass: {
                    popup: 'swal2-popup-mdrrmo',
                    title: 'swal2-title-mdrrmo',
                    content: 'swal2-content-mdrrmo'
                },
                html: `
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> This action cannot be undone.
                    </div>
                    <p><strong>${text}</strong></p>
                    <div class="alert alert-info">
                        <strong>Item to delete:</strong> ${itemName}
                    </div>
                `,
                focusConfirm: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm();
                }
            });
        }

        /**
         * Show loading dialog
         * @param {string} message - Loading message
         */
        function showLoading(message = 'Processing...') {
            Swal.fire({
                title: message,
                allowEscapeKey: false,
                allowOutsideClick: false,
                showConfirmButton: false,
                background: '#fafbfb',
                color: '#2f3833',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        /**
         * Close loading dialog
         */
        function closeLoading() {
            Swal.close();
        }

        // Display Laravel session messages as toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showSuccessToast('{{ session('success') }}');
            @endif

            @if(session('error'))
                showErrorToast('{{ session('error') }}');
            @endif

            @if(session('warning'))
                showWarningToast('{{ session('warning') }}');
            @endif

            @if(session('info'))
                showInfoToast('{{ session('info') }}');
            @endif
        });


    </script>

    @stack('scripts')
</body>
</html>
