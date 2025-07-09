<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use App\Models\LoginAttempt;
use App\Models\ActivityLog;

class TwoFactorController extends Controller
{
    public function showVerifyForm()
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

    public function verify(Request $request)
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
            LoginAttempt::create([
                'email' => $user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'successful' => true,
                'details' => [
                    'login_method' => 'email_password_2fa',
                    'municipality' => $user->municipality,
                    'role' => $user->role
                ],
                'attempted_at' => now()
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => '2fa_verified_login',
                'model_type' => 'Authentication',
                'model_id' => $user->id,
                'description' => "MDRRMO System: 2FA verified login for {$user->full_name} ({$user->municipality})",
                'new_values' => json_encode(['verification_method' => 'email_otp']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $welcomeMessage = $user->isAdmin()
                ? "Welcome back, Administrator {$user->full_name}!"
                : "Welcome back, {$user->full_name} - {$user->municipality} MDRRMO!";

            return redirect('/dashboard')->with('login_success', $welcomeMessage);
        }

        return back()->withErrors(['two_factor_code' => 'Invalid or expired OTP code.']);
    }

    public function resendCode(Request $request)
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
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => '2fa_code_resent',
                'model_type' => 'Authentication',
                'model_id' => $user->id,
                'description' => "MDRRMO System: 2FA code resent for {$user->full_name}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return back()->with('success', 'A new 2FA code has been sent to your email.');
        }

        return back()->withErrors(['email' => 'User not found.']);
    }
}
