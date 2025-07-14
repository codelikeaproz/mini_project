<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Mail\TwoFactorCodeMail;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    // ========================================
    // LOGIN & LOGOUT METHODS
    // ========================================

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            $this->logAttempt($request->email, false, ['reason' => 'user_not_found']);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if account is locked
        if ($user->isAccountLocked()) {
            $this->logAttempt($request->email, false, ['reason' => 'account_locked']);
            return back()->withErrors([
                'email' => 'Account is temporarily locked due to failed login attempts. Please try again later.',
            ]);
        }

        // Check if account is active
        if (!$user->is_active) {
            $this->logAttempt($request->email, false, ['reason' => 'account_inactive']);
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact your administrator.',
            ]);
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            // Check if email is verified
            if (!$user->is_verified) {
                Auth::logout();
                $this->logAttempt($request->email, false, ['reason' => 'email_not_verified']);
                return redirect('/login')->with('error', 'Please verify your email before logging in to the MDRRMO system.');
            }

            // Reset failed login attempts
            $user->resetFailedLogins();

            // Generate and send 2FA code
            $user->generateTwoFactorCode();
            Mail::to($user->email)->send(new TwoFactorCodeMail($user));

            // Logout temporarily until 2FA is verified
            Auth::logout();

            // Store email in session for 2FA
            session(['2fa_email' => $user->email, '2fa_timestamp' => now()->timestamp]);

            return redirect()->route('2fa.verify.form')->with([
                'message' => 'A 2FA verification code has been sent to your email.',
                'email' => $user->email
            ]);
        }

        // Increment failed login attempts
        if ($user) {
            $user->incrementFailedLogins();
        }

        // Log failed attempt
        $this->logAttempt($request->email, false, ['reason' => 'invalid_credentials']);

        throw ValidationException::withMessages([
            'password' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout activity
        if ($user) {
            $this->logActivity('logout', $user, [
                'logout_method' => 'manual',
                'municipality' => $user->municipality
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out from MDRRMO system.');
    }

    // ========================================
    // REGISTRATION METHODS (Admin Only)
    // ========================================

    public function showRegister()
    {
        // Only allow admin users to access registration
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in as an admin to register new MDRRMO staff.');
        }

        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can register new MDRRMO staff members.');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'position' => $validated['position'] ?? null,
            'role' => 'mdrrmo_staff', // Default role for MDRRMO system
            'municipality' => 'Maramag', // Default municipality from ProjectGuide
            'verification_token' => Str::random(64),
            'is_verified' => false,
            'is_active' => true,
        ]);

        // Send verification email
        $user->notify(new EmailVerificationNotification($user));

        // Log registration
        $this->logActivity('mdrrmo_staff_registered', $user, [
            'registration_method' => 'email_password',
            'municipality' => $user->municipality,
            'position' => $user->position
        ]);

        return redirect()->route('users.index')->with('success',
            "MDRRMO staff '{$user->full_name}' registered successfully! Verification email sent to {$user->email}."
        );
    }

    // ========================================
    // TWO-FACTOR AUTHENTICATION METHODS
    // ========================================

    public function showTwoFactorForm()
    {
        $email = session('2fa_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again to MDRRMO system.');
        }

        // Check if 2FA session hasn't expired (30 minutes)
        $timestamp = session('2fa_timestamp');
        if ($timestamp && (now()->timestamp - $timestamp) > 1800) {
            session()->forget(['2fa_email', '2fa_timestamp']);
            return redirect()->route('login')->with('error', 'Session expired. Please login again to MDRRMO system.');
        }

        return view('auth.two-factor')->with('email', $email);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);

        $email = $request->email ?? session('2fa_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again to MDRRMO system.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['two_factor_code' => 'Invalid user session.']);
        }

        if ($user->isTwoFactorCodeValid($request->two_factor_code)) {
            // Login user
            Auth::login($user);

            // Clear 2FA code and session data
            $user->clearTwoFactorCode();
            session()->forget(['2fa_email', '2fa_timestamp']);

            // Log successful login
            $this->logAttempt($user->email, true, [
                'login_method' => 'email_password_2fa',
                'municipality' => $user->municipality,
                'role' => $user->role
            ]);

            // Log activity
            $this->logActivity('2fa_verified_login', $user, ['verification_method' => 'email_otp']);

            $welcomeMessage = $user->isAdmin()
                ? "Welcome back, Administrator {$user->full_name}!"
                : "Welcome back, {$user->full_name} - {$user->municipality} MDRRMO!";

            return redirect('/dashboard')->with('login_success', $welcomeMessage);
        }

        return back()->withErrors(['two_factor_code' => 'Invalid or expired OTP code.']);
    }

    public function resendTwoFactorCode(Request $request)
    {
        $email = $request->email ?? session('2fa_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again to MDRRMO system.');
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->generateTwoFactorCode();
            Mail::to($user->email)->send(new TwoFactorCodeMail($user));

            // Refresh session timestamp
            session(['2fa_timestamp' => now()->timestamp]);

            // Log resend activity
            $this->logActivity('2fa_code_resent', $user);

            return back()->with('success', 'A new 2FA code has been sent to your email.');
        }

        return back()->withErrors(['email' => 'User not found.']);
    }

    // ========================================
    // EMAIL VERIFICATION METHODS
    // ========================================

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid verification token.');
        }

        if ($user->is_verified) {
            return redirect('/login')->with('info', 'Email already verified. You can now log in to the MDRRMO system.');
        }

        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        // Log email verification
        $this->logActivity('email_verified', $user);

        return redirect('/login')
            ->with('success', 'Email verified successfully! You can now log in to the MDRRMO system.')
            ->with('verified_email', $user->email);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->is_verified) {
            $user->notify(new EmailVerificationNotification($user));
            return back()->with('success', 'Verification email resent!');
        }

        return back()->with('error', 'Unable to resend verification email.');
    }

    // ========================================
    // PASSWORD RESET METHODS
    // ========================================

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Log password reset
                $this->logActivity('password_reset', $user);

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function logAttempt($email, $successful, $details = [])
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'successful' => $successful,
            'details' => $details,
            'attempted_at' => now()
        ]);
    }

    private function logActivity($action, $user, $details = [])
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: {$action} for {$user->full_name} ({$user->municipality})",
            'new_values' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
