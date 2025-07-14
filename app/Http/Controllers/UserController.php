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
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $users = User::when($search, function ($query) use ($search) {
            return $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('municipality', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%");
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,mdrrmo_staff',
            'phone_number' => 'nullable|string|max:20',
            'municipality' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'],
            'municipality' => $validated['municipality'],
            'position' => $validated['position'],
            'verification_token' => \Str::random(64),
            'is_verified' => false,
            'is_active' => true,
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        // Log admin activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: User created - {$user->full_name} ({$user->municipality})",
            'new_values' => json_encode([
                'created_user_name' => $user->full_name,
                'created_user_email' => $user->email,
                'created_user_role' => $user->role,
                'created_user_municipality' => $user->municipality,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'MDRRMO user created successfully! Verification email sent.');
    }

    public function show(User $user)
    {
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('users.show', compact('user', 'recentActivities'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,mdrrmo_staff',
            'phone_number' => 'nullable|string|max:20',
            'municipality' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $oldValues = $user->toArray();

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'],
            'municipality' => $validated['municipality'],
            'position' => $validated['position'],
            'is_active' => $request->has('is_active'),
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
            'description' => "MDRRMO System: User updated - {$user->full_name} ({$user->municipality})",
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($updateData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('users.index')
            ->with('success', 'MDRRMO user updated successfully!');
    }

    public function destroy(Request $request, User $user)
    {
        // Prevent deletion of admin users if they're the last admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($adminCount <= 1) {
                $message = 'Cannot delete the last active admin user.';

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return back()->with('error', $message);
            }
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            $message = 'You cannot delete your own account.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $userName = $user->full_name;
        $userMunicipality = $user->municipality;

        // Log before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: User deleted - {$userName} ({$userMunicipality})",
            'old_values' => json_encode($user->toArray()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $user->delete();

        $successMessage = "MDRRMO user '{$userName}' deleted successfully!";

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }

        return back()->with('success', $successMessage);
    }

    public function resendVerification(Request $request, User $user)
    {
        // Only admins can resend verification for other users
        if (!auth()->user()->isAdmin()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized action'], 403);
            }
            return back()->with('error', 'Unauthorized action');
        }

        // Check if user is already verified
        if ($user->is_verified) {
            $message = 'User email is already verified';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        try {
            // Generate new verification token and send email
            $user->sendEmailVerificationNotification();

            // Log admin activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'verification_email_resent',
                'model_type' => 'App\Models\User',
                'model_id' => $user->id,
                'description' => "MDRRMO System: Verification email resent for {$user->full_name} by admin",
                'new_values' => json_encode([
                    'target_user_email' => $user->email,
                    'target_user_name' => $user->full_name,
                    'admin_action' => 'resend_verification'
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $successMessage = "Verification email has been resent to {$user->email}";

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $successMessage]);
            }

            return back()->with('success', $successMessage);

        } catch (\Exception $e) {
            $errorMessage = 'Failed to resend verification email: ' . $e->getMessage();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }

            return back()->with('error', $errorMessage);
        }
    }

    public function adminProfile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function updateAdminProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldValues = $user->toArray();

        $updateData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'position' => $validated['position'],
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
            'action' => 'admin_profile_updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: Admin profile updated - {$user->full_name}",
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($updateData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Admin profile updated successfully!');
    }
}
