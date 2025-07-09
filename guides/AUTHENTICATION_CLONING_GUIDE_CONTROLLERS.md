# Authentication Controllers Implementation

## 🎯 **Step 4: Create Controllers**

### **4.1 Main Authentication Controller**
**app/Http/Controllers/AuthController.php**
```php
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
        if (Auth::check()) {
            return redirect()->route('dashboard');
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

        // Check for account lockout
        if ($user && $this->isLockedOut($request->email)) {
            return back()->withErrors([
                'email' => 'Too many failed login attempts. Please try again in 15 minutes.',
            ]);
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $user = Auth::user();

            // Check if email is verified
            if (!$user->is_verified) {
                Auth::logout();
                $this->logAttempt($request->email, false, ['reason' => 'email_not_verified']);
                return redirect('/login')->with('error', 'Please verify your email before logging in.');
            }

            // Generate and send 2FA code
            $user->generateTwoFactorCode();
            Mail::to($user->email)->send(new TwoFactorCodeMail($user));

            // Logout temporarily until 2FA is verified
            Auth::logout();

            // Store email in session for 2FA
            session(['2fa_email' => $user->email, '2fa_timestamp' => now()->timestamp]);

            return redirect()->route('2fa.verify.form')->with([
                'message' => 'A 2FA code has been sent to your email.',
                'email' => $user->email
            ]);
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'verification_token' => Str::random(64),
            'is_verified' => false,
        ]);

        // Send verification email
        $user->notify(new EmailVerificationNotification($user));

        // Log registration
        $this->logActivity('register', $user, ['registration_method' => 'email_password']);

        return redirect('/login')->with('success', 
            'Registration successful! Please check your email for verification link.'
        );
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log logout activity
        if ($user) {
            $this->logActivity('logout', $user, ['logout_method' => 'manual']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('user.dashboard');
    }

    // Helper methods
    private function isLockedOut($email)
    {
        $attempts = LoginAttempt::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes(15))
            ->count();

        return $attempts >= 5;
    }

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
            'details' => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
```

### **4.2 Two-Factor Authentication Controller**
**app/Http/Controllers/TwoFactorController.php**
```php
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
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Check if 2FA session hasn't expired (30 minutes)
        $timestamp = session('2fa_timestamp');
        if ($timestamp && (now()->timestamp - $timestamp) > 1800) {
            session()->forget(['2fa_email', '2fa_timestamp']);
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
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
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['two_factor_code' => 'Invalid user.']);
        }

        if ($user->isTwoFactorCodeValid($request->two_factor_code)) {
            // Login user
            Auth::login($user);

            // Clear 2FA session data
            session()->forget(['2fa_email', '2fa_timestamp']);

            // Log successful login
            LoginAttempt::create([
                'email' => $user->email,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'successful' => true,
                'details' => ['login_method' => 'email_password_2fa'],
                'attempted_at' => now()
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => '2fa_verified',
                'model_type' => 'Authentication',
                'model_id' => $user->id,
                'details' => json_encode(['verification_method' => 'email_otp']),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return redirect('/dashboard')->with('login_success', "Welcome back, {$user->name}!");
        }

        return back()->withErrors(['two_factor_code' => 'Invalid or expired OTP code.']);
    }

    public function resendCode(Request $request)
    {
        $email = $request->email ?? session('2fa_email');

        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->generateTwoFactorCode();
            Mail::to($user->email)->send(new TwoFactorCodeMail($user));

            // Refresh session timestamp
            session(['2fa_timestamp' => now()->timestamp]);

            return back()->with('success', 'A new 2FA code has been sent to your email.');
        }

        return back()->withErrors(['email' => 'User not found.']);
    }
}
```

### **4.3 Email Verification Controller**
**app/Http/Controllers/EmailVerificationController.php**
```php
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
            'details' => json_encode(['verification_method' => 'email_token']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect('/login')
            ->with('success', 'Email verified successfully! You can now log in.')
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

        return back()->with('success', 'Verification email has been resent.');
    }
}
```

### **4.4 User Management Controller**
**app/Http/Controllers/UserController.php**
```php
<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        
        $users = User::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'verification_token' => \Str::random(64),
            'is_verified' => false,
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        // Log admin activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'details' => json_encode([
                'created_user_name' => $user->name,
                'created_user_email' => $user->email,
                'created_user_role' => $user->role,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully! Verification email sent.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Log admin activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'details' => json_encode([
                'updated_user_name' => $user->name,
                'updated_user_email' => $user->email,
                'updated_user_role' => $user->role,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deletion of admin users
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin users cannot be deleted.');
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        
        // Log before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'details' => json_encode([
                'deleted_user_name' => $userName,
                'deleted_user_email' => $user->email,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $user->delete();

        return back()->with('success', "User '{$userName}' deleted successfully!");
    }
}
```

### **4.5 User Dashboard Controller**
**app/Http/Controllers/UserDashboardController.php**
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's recent activities
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.dashboard', compact('user', 'recentActivities'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath;
        }

        $user->update($updateData);

        // Log profile update
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'profile_updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'details' => json_encode(['updated_fields' => array_keys($updateData)]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
```

### **4.6 Login Attempts Controller**
**app/Http/Controllers/LoginAttemptController.php**
```php
<?php
namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use Illuminate\Http\Request;

class LoginAttemptController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $loginAttempts = LoginAttempt::with('user')
            ->when($search, function ($query) use ($search) {
                return $query->where('email', 'like', "%{$search}%")
                            ->orWhere('ip_address', 'like', "%{$search}%");
            })
            ->orderBy('attempted_at', 'desc')
            ->paginate(20);

        return view('admin.login-attempts', compact('loginAttempts', 'search'));
    }

    public static function logAttempt($email, $successful = false, $details = [])
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

    public static function getRecentFailedAttempts($email, $minutes = 15)
    {
        return LoginAttempt::where('email', $email)
            ->failed()
            ->recent($minutes)
            ->count();
    }

    public static function isLockedOut($email, $maxAttempts = 5, $lockoutMinutes = 15)
    {
        $failedAttempts = self::getRecentFailedAttempts($email, $lockoutMinutes);
        return $failedAttempts >= $maxAttempts;
    }
}
```

### **4.7 Password Reset Controller**
**app/Http/Controllers/PasswordResetController.php**
```php
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

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
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

                // Log password reset
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset',
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                    'details' => json_encode(['reset_method' => 'email_token']),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}
```

This completes the controllers section. Would you like me to continue with the Mail classes, Notifications, and Views sections? 
