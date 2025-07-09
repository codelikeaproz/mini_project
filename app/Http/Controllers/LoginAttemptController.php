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

        // Get statistics
        $stats = [
            'total_attempts' => LoginAttempt::count(),
            'successful_attempts' => LoginAttempt::where('successful', true)->count(),
            'failed_attempts' => LoginAttempt::where('successful', false)->count(),
            'recent_failed' => LoginAttempt::where('successful', false)
                                          ->where('attempted_at', '>=', now()->subHours(24))
                                          ->count(),
        ];

        return view('admin.login-attempts', compact('loginAttempts', 'search', 'stats'));
    }
}
