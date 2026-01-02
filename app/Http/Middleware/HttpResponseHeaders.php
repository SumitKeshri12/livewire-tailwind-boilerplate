<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HttpResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Generate CSP Nonce (PER REQUEST — CSP spec compliant)
        |--------------------------------------------------------------------------
        | - MUST be unique per response
        | - Never persisted in session
        | - Prevents XSS replay attacks
        */
        $nonce = base64_encode(random_bytes(16));

        /*
        |--------------------------------------------------------------------------
        | 2. Environment Strategy
        |--------------------------------------------------------------------------
        | Local:
        | - CSP disabled to avoid Vite HMR / Livewire dev friction
        |
        | Non-Local (UAT / PROD):
        | - CSP enabled
        | - Nonce injected
        */
        $isLocal = App::environment('local');
        $shouldApplyCsp = ! $isLocal;

        // Share nonce with Blade views
        View::share('nonce', $shouldApplyCsp ? $nonce : null);

        // Make nonce available via request attributes if needed
        if ($shouldApplyCsp) {
            $request->attributes->set('csp_nonce', $nonce);
        }

        /** @var Response $response */
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | 3. Security Headers (Non-Local Only)
        |--------------------------------------------------------------------------
        */
        if ($shouldApplyCsp) {
            // Remove information disclosure
            $response->headers->remove('X-Powered-By');

            // Clickjacking protection
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN', true);

            // MIME sniffing protection
            $response->headers->set('X-Content-Type-Options', 'nosniff', true);

            // Referrer policy
            $response->headers->set(
                'Referrer-Policy',
                'strict-origin-when-cross-origin',
                true
            );

            // Permissions policy
            $response->headers->set(
                'Permissions-Policy',
                'geolocation=(self)',
                true
            );

            /*
            |--------------------------------------------------------------------------
            | HSTS (HTTPS Only)
            |--------------------------------------------------------------------------
            */
            $appUrlScheme = parse_url(config('app.url'), PHP_URL_SCHEME);

            if (
                App::isProduction()
                || $request->isSecure()
                || $appUrlScheme === 'https'
            ) {
                $response->headers->set(
                    'Strict-Transport-Security',
                    'max-age=31536000; includeSubDomains',
                    true
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Content Security Policy (Livewire + Alpine + Flux Safe)
            |--------------------------------------------------------------------------
            |
            | REQUIRED:
            | - 'unsafe-eval' → Alpine.js runtime requirement
            | - nonce         → Inline boot scripts (Livewire, Flux)
            |
            | IMPORTANT:
            | - NO 'unsafe-inline' → keeps nonce effective
            | - Inline scripts must be guarded to avoid re-execution
            |
            */
            $csp = implode('; ', [
                "default-src 'self'",

                "script-src
                    'self'
                    'nonce-{$nonce}'
                    'unsafe-eval'
                    https://code.jquery.com
                    https://unpkg.com
                    https://www.google.com
                    https://www.gstatic.com
                ",

                "style-src
                    'self'
                    'unsafe-inline'
                    https://fonts.bunny.net
                    https://unpkg.com
                ",

                "font-src
                    'self'
                    data:
                    https://fonts.bunny.net
                ",

                "img-src
                    'self'
                    data:
                    https://fluxui.dev
                ",

                "connect-src
                    'self'
                ",

                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
                "frame-src 'self' https://www.google.com",
            ]);

            $response->headers->set('Content-Security-Policy', $csp, true);
        }

        return $response;
    }
}
