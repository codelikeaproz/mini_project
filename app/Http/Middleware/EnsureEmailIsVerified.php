<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() ||
            (! $request->user()->hasVerifiedEmail() &&
             ! $request->routeIs('verification.*') &&
             ! $request->routeIs('logout') &&
             ! $request->routeIs('verify.email'))) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
