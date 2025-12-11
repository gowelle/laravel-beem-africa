<?php

declare(strict_types=1);

namespace Gowelle\BeemAfrica\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to verify the Beem webhook signature.
 */
class VerifyBeemSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredSecret = config('beem.webhook.secret');

        // If no secret is configured, allow the request
        if (empty($configuredSecret)) {
            return $next($request);
        }

        $providedToken = $request->header('beem-secure-token');

        if ($providedToken !== $configuredSecret) {
            abort(401, 'Invalid Beem secure token');
        }

        return $next($request);
    }
}
