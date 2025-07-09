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
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

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

    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('admin.dashboard', compact('user'));
        }

        return view('user.dashboard', compact('user'));
    }

    // Helper methods
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
            'model_type' => 'Authentication',
            'model_id' => $user->id,
            'description' => "MDRRMO System: {$action} for {$user->full_name}",
            'old_values' => null,
            'new_values' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
