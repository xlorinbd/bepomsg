<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKYC
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only apply to customers who are not admins
        if ($user && $user->is_customer && !$user->is_admin) {
            
            // If verification is pending or rejected
            if ($user->verification_status === 'pending' || $user->verification_status === 'rejected') {
                
                // Routes that are allowed even when verification is pending/rejected
                $allowedRoutes = [
                    'user.home',
                    'customer.verification.resubmit',
                    'user.account',
                    'user.account.update',
                    'user.account.update_information',
                    'user.avatar',
                    'user.remove_avatar',
                    'logout',
                    'verification.notice',
                    'verification.verify',
                    'verification.send',
                ];

                $currentRoute = $request->route()->getName();

                // If the current route is NOT in the allowed list, redirect to dashboard
                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('user.home')->with('flash_error', 'Your account verification is pending or was rejected. Please complete the verification process to access other features.');
                }

            }
        }

        return $next($request);
    }
}
