<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MDRRMO Maramag - Two-Factor Authentication</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .tfa-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tfa-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .tfa-header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .tfa-body {
            padding: 2rem;
        }
        .verification-code {
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
            font-weight: bold;
            border-radius: 0.5rem;
            padding: 1rem;
            border: 2px solid #e2e8f0;
        }
        .verification-code:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }
        .btn-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .btn-outline-success {
            border-color: #22c55e;
            color: #22c55e;
            border-radius: 0.5rem;
        }
        .btn-outline-success:hover {
            background-color: #22c55e;
            border-color: #22c55e;
        }
        .countdown-timer {
            font-weight: bold;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            color: #92400e;
            display: inline-block;
            min-width: 120px;
        }
        .countdown-timer.warning {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            border-color: #dc2626;
            color: #991b1b;
        }
        .countdown-timer.expired {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #dc2626;
            color: #991b1b;
        }
        .timer-container {
            text-align: center;
            margin-bottom: 1rem;
        }
        .timer-label {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="tfa-container">
        <div class="tfa-card">
            <div class="tfa-header">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h3 class="mb-1">Two-Factor Authentication</h3>
                <p class="mb-0">MDRRMO Security Verification</p>
            </div>

            <div class="tfa-body">
                <!-- Display Messages -->
                @if(session('message'))
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>{{ session('message') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                <div class="text-center mb-4">
                    <p class="mb-3">
                        We've sent a 6-digit verification code to:<br>
                        <strong>{{ $email }}</strong>
                    </p>

                    <!-- Countdown Timer -->
                    <div class="timer-container">
                        <div class="timer-label">
                            <i class="fas fa-clock me-1"></i>Code expires in:
                        </div>
                        <div id="countdown-timer" class="countdown-timer">
                            <span id="timer-display">10:00</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('2fa.verify') }}" id="verify-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-4">
                        <label for="two_factor_code" class="form-label text-center d-block">
                            <i class="fas fa-key me-2"></i>Enter Verification Code
                        </label>
                        <input type="text"
                               class="form-control verification-code @error('two_factor_code') is-invalid @enderror"
                               id="two_factor_code"
                               name="two_factor_code"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               required
                               autocomplete="off"
                               placeholder="000000">
                        @error('two_factor_code')
                            <div class="invalid-feedback text-center">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-3" id="verify-button">
                        <i class="fas fa-check me-2"></i>Verify and Login
                    </button>
                </form>

                <!-- Resend Code Form -->
                <form method="POST" action="{{ route('2fa.resend') }}" class="text-center" id="resend-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <p class="mb-2">Didn't receive the code?</p>
                    <button type="submit" class="btn btn-outline-success" id="resend-button">
                        <i class="fas fa-redo me-2"></i>Resend Code
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted">
                        <i class="fas fa-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Countdown Timer Configuration
        let countdownDuration = 10 * 60; // 10 minutes in seconds
        let countdownInterval;

        // Get elements
        const timerDisplay = document.getElementById('timer-display');
        const countdownTimer = document.getElementById('countdown-timer');
        const verifyForm = document.getElementById('verify-form');
        const verifyButton = document.getElementById('verify-button');
        const resendForm = document.getElementById('resend-form');
        const resendButton = document.getElementById('resend-button');
        const codeInput = document.getElementById('two_factor_code');

        // Format time display
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }

        // Update countdown display
        function updateCountdown() {
            timerDisplay.textContent = formatTime(countdownDuration);

            // Change appearance based on remaining time
            if (countdownDuration <= 0) {
                // Timer expired
                countdownTimer.className = 'countdown-timer expired';
                timerDisplay.innerHTML = '<i class="fas fa-times me-1"></i>EXPIRED';

                // Disable form
                verifyForm.style.opacity = '0.5';
                verifyButton.disabled = true;
                verifyButton.innerHTML = '<i class="fas fa-times me-2"></i>Code Expired';
                codeInput.disabled = true;

                // Show resend option
                resendForm.style.display = 'block';

                clearInterval(countdownInterval);
                return;
            } else if (countdownDuration <= 60) {
                // Warning state (last minute)
                countdownTimer.className = 'countdown-timer warning';
            } else if (countdownDuration <= 180) {
                // Caution state (last 3 minutes)
                countdownTimer.className = 'countdown-timer warning';
            }

            countdownDuration--;
        }

        // Start countdown
        function startCountdown() {
            updateCountdown(); // Initial call
            countdownInterval = setInterval(updateCountdown, 1000);
        }

        // Reset countdown (for resend)
        function resetCountdown() {
            clearInterval(countdownInterval);
            countdownDuration = 10 * 60; // Reset to 10 minutes

            // Reset form state
            verifyForm.style.opacity = '1';
            verifyButton.disabled = false;
            verifyButton.innerHTML = '<i class="fas fa-check me-2"></i>Verify and Login';
            codeInput.disabled = false;
            countdownTimer.className = 'countdown-timer';

            // Clear input
            codeInput.value = '';
            codeInput.focus();

            startCountdown();
        }

        // Handle resend form submission
        resendForm.addEventListener('submit', function(e) {
            // Show loading state
            resendButton.disabled = true;
            resendButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

            // Note: The form will submit normally, and on page reload, countdown will restart
        });

        // Auto-format verification code input
        codeInput.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Auto-submit when 6 digits are entered (only if timer hasn't expired)
            if (this.value.length === 6 && countdownDuration > 0) {
                verifyForm.submit();
            }
        });

        // Auto-focus on verification code input
        codeInput.focus();

        // Start the countdown timer
        startCountdown();

        // Handle page visibility change (pause/resume timer when tab is hidden/visible)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(countdownInterval);
            } else {
                // Resume countdown if not expired
                if (countdownDuration > 0) {
                    countdownInterval = setInterval(updateCountdown, 1000);
                }
            }
        });
    </script>
</body>
</html>
