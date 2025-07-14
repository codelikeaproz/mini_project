<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;
use App\Models\User;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        // Remove role middleware - let routes handle access control
        $this->middleware(['auth', 'verified']);
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Get user's recent activities
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get some basic stats for MDRRMO staff dashboard
        $stats = [
            'total_staff' => User::where('municipality', $user->municipality)->count(),
            'active_staff' => User::where('municipality', $user->municipality)
                                 ->where('is_active', true)->count(),
        ];

        return view('user.dashboard', compact('user', 'recentActivities', 'stats'));
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
            'action' => 'profile_updated',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'description' => "MDRRMO System: Profile updated - {$user->full_name} ({$user->municipality})",
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($updateData),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
