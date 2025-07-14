<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access MDRRMO system.');
        }

        $user = Auth::user();

        // Check if account is locked
        if (method_exists($user, 'isAccountLocked') && $user->isAccountLocked()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Account is temporarily locked due to failed login attempts.');
        }

        // Check if account is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Contact administrator.');
        }

        // Handle multiple roles - either from multiple parameters or comma-separated string
        $allowedRoles = [];
        foreach ($roles as $role) {
            if (str_contains($role, ',')) {
                // Handle comma-separated roles in single parameter
                $allowedRoles = array_merge($allowedRoles, array_map('trim', explode(',', $role)));
            } else {
                $allowedRoles[] = trim($role);
            }
        }

        // Clean user role (remove any whitespace)
        $userRole = trim($user->role);

        // Check if user has any of the allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            // Enhanced debug logging
            Log::warning('403 Access Denied - Role Check Failed', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => "'{$userRole}'", // Wrapped in quotes to see spaces
                'user_role_hex' => bin2hex($userRole), // To catch hidden characters
                'user_role_length' => strlen($userRole),
                'required_roles_raw' => $roles,
                'allowed_roles' => $allowedRoles,
                'url' => $request->url(),
                'route_name' => $request->route() ? $request->route()->getName() : 'no_route',
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            abort(403, "Access denied. Required roles: [" . implode(', ', $allowedRoles) . "]. Your role: '{$userRole}'");
        }

        return $next($request);
    }
}
