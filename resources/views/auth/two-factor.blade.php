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
        .countdown {
            font-weight: bold;
            color: #dc2626;
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
                    <p class="text-muted small">
                        <i class="fas fa-clock me-1"></i>Code expires in 10 minutes
                    </p>
                </div>

                <form method="POST" action="{{ route('2fa.verify') }}">
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

                    <button type="submit" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-check me-2"></i>Verify and Login
                    </button>
                </form>

                <!-- Resend Code Form -->
                <form method="POST" action="{{ route('2fa.resend') }}" class="text-center">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <p class="mb-2">Didn't receive the code?</p>
                    <button type="submit" class="btn btn-outline-success">
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
        // Auto-format verification code input
        document.getElementById('two_factor_code').addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Auto-submit when 6 digits are entered
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Auto-focus on verification code input
        document.getElementById('two_factor_code').focus();
    </script>
</body>
</html>
