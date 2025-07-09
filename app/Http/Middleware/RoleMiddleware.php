<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access MDRRMO system.');
        }

        $user = Auth::user();

        // Check if account is locked
        if ($user->isAccountLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account is temporarily locked due to failed login attempts.');
        }

        // Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Contact administrator.');
        }

        // Check role permission - support multiple roles separated by comma
        $allowedRoles = explode(',', $roles);
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Unauthorized access to MDRRMO system.');
        }

        return $next($request);
    }
}
