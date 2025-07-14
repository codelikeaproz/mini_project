<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MDRRMO Maramag - Accident Reporting System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Chart.js (for analytics) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- MDRRMO Custom Styles -->
    <style>
        :root {
            --primary-50: #f8faf9;
            --primary-100: #f1f5f3;
            --primary-200: #e2e8e4;
            --primary-300: #cbd5d1;
            --primary-400: #9ca8a3;
            --primary-500: #6b7671;
            --primary-600: #556159;
            --primary-700: #424d47;
            --primary-800: #2f3833;
            --primary-900: #1a201c;

            --success: #556159;
            --warning: #8b7355;
            --info: #6b7671;
            --danger: #7a5a5a;

            --gray-50: #fafbfb;
            --gray-100: #f4f6f5;
            --gray-200: #e9ece9;
            --gray-300: #d3d7d3;
            --gray-400: #a1a6a1;
            --gray-500: #6f746f;
            --gray-600: #5c615c;
            --gray-700: #4a4f4a;
            --gray-800: #383d38;
            --gray-900: #262a26;
        }

        body {
            background-color: var(--gray-50);
            color: var(--gray-700);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Navigation Styles */
        .navbar-mdrrmo {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: 80px;
            padding: 1rem 0 !important;
        }

        .navbar-brand {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            letter-spacing: -0.025em;
            color: white !important;
        }

        .navbar-nav .nav-link {
            color: white !important;
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

        .dropdown-menu {
            border: 0;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
            margin-top: 0.5rem;
            padding: 0.75rem 0;
        }

        .dropdown-item {
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            color: var(--gray-700);
        }

        .dropdown-item:hover {
            background-color: var(--primary-100);
            transform: translateX(2px);
            color: var(--primary-800);
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--primary-500);
            border-color: var(--primary-500);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-600);
            border-color: var(--primary-600);
        }

        .btn-outline-primary {
            color: var(--primary-600);
            border-color: var(--primary-400);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-500);
            border-color: var(--primary-500);
        }

        /* Card Styles */
        .card {
            border: 1px solid var(--gray-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }

        .card-header {
            background-color: var(--primary-50);
            border-bottom: 1px solid var(--gray-200);
            color: var(--primary-800);
            font-weight: 600;
        }

        /* Alert Styles */
        .alert-success {
            background-color: var(--primary-50);
            color: var(--primary-800);
            border-left: 4px solid var(--success);
            border-radius: 0.5rem;
        }

        .alert-danger {
            background-color: var(--gray-100);
            color: var(--danger);
            border-left: 4px solid var(--danger);
            border-radius: 0.5rem;
        }

        .alert-warning {
            background-color: var(--gray-100);
            color: var(--warning);
            border-left: 4px solid var(--warning);
            border-radius: 0.5rem;
        }

        .alert-info {
            background-color: var(--primary-50);
            color: var(--primary-700);
            border-left: 4px solid var(--info);
            border-radius: 0.5rem;
        }

        /* Form Styles */
        .form-control:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(107, 118, 113, 0.1);
        }

        .form-select:focus {
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(107, 118, 113, 0.1);
        }

        /* Table Styles */
        .table-hover tbody tr:hover {
            background-color: var(--primary-50);
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary-100) 0%, var(--gray-100) 100%);
            border-bottom: 1px solid var(--gray-200);
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .page-title {
            color: var(--primary-800);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-weight: 400;
        }

        /* Status Badges */
        .badge.status-pending { background-color: var(--warning); }
        .badge.status-responding { background-color: var(--info); }
        .badge.status-resolved { background-color: var(--success); }
        .badge.status-closed { background-color: var(--gray-500); }

        /* Content Area */
        .main-content {
            min-height: calc(100vh - 160px);
            padding: 2rem 0;
        }

        /* Footer */
        .footer {
            background-color: var(--gray-800);
            color: var(--gray-300);
            text-align: center;
            padding: 1.5rem 0;
            margin-top: auto;
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
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} MDRRMO Maramag. All rights reserved. | Accident Reporting & Vehicle Utilization System</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JavaScript -->
    <script>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.main-content').classList.add('fade-in');
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
