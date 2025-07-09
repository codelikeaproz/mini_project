<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;

class EmailVerificationController extends Controller
{
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Invalid or expired verification token.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('info', 'Your email is already verified.');
        }

        // Mark email as verified
        $user->markEmailAsVerified();
        $user->update(['is_verified' => true, 'verification_token' => null]);

        // Log verification activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'email_verified',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: Email verified for {$user->full_name} ({$user->municipality})",
            'new_values' => json_encode(['verification_method' => 'email_token']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/login')
            ->with('success', 'Email verified successfully! You can now log in to the MDRRMO system.')
            ->with('verified_email', $user->email);
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'Email is already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email has been resent to your MDRRMO account.');
    }
}
