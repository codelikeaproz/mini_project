<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ActivityLog;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Log password reset request
            $user = User::where('email', $request->email)->first();
            if ($user) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset_requested',
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'description' => "MDRRMO System: Password reset requested for {$user->full_name}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your MDRRMO email address.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
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
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Reset failed login attempts
                $user->update([
                    'failed_login_attempts' => 0,
                    'locked_until' => null
                ]);

                // Log password reset
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset_completed',
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'description' => "MDRRMO System: Password reset completed for {$user->full_name}",
                    'new_values' => json_encode(['reset_method' => 'email_token']),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully! You can now log in to MDRRMO system.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
