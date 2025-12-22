<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HttpResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Generate CSP Nonce (VAPT Enhancement)
        $nonce = base64_encode(random_bytes(16));
        
        // In local dev, we don't want the nonce to change on every request because it breaks wire:navigate
        // (scripts get reloaded because the nonce attribute changes).
        $shouldUseNonce = !App::environment('local');
        $viewNonce = $shouldUseNonce ? $nonce : '';

        // Share with all views so we can use it in Blade (e.g., <script nonce="{{ $nonce }}">)
        \Illuminate\Support\Facades\View::share('nonce', $viewNonce);

        // Store nonce in request attributes for Livewire access
        if ($shouldUseNonce) {
            $request->attributes->set('csp_nonce', $nonce);
        }

        $response = $next($request);

        // Remove X-Powered-By header (Banner Disclosure mitigation)
        $response->headers->remove('X-Powered-By');

        // Set Security Headers (force replace with true parameter)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', true);
        $response->headers->set('X-Content-Type-Options', 'nosniff', true);

        // Enforce HTTPS (HSTS) - in production, staging, secure requests, or when APP_URL uses https
        $appUrlScheme = parse_url(config('app.url'), PHP_URL_SCHEME);
        if (App::isProduction() || $request->isSecure() || $appUrlScheme === 'https') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains',
                true
            );
        }

        // In local development, we remove the nonce to allow 'unsafe-inline' to work
        // because Vite/Dev tools usually inject inline scripts without nonces.
        // In Production/UAT, the nonce is present, so 'unsafe-inline' is rightfully ignored for stricter security.
        $nonceDirective = App::environment('local') ? '' : "'nonce-{$nonce}'";

        $upgradeDirective = App::environment('local') ? '' : 'upgrade-insecure-requests;';
        $csp = "default-src 'self'; {$upgradeDirective} " .
            "script-src 'self' {$nonceDirective} 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://unpkg.com https://www.google.com https://www.gstatic.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://unpkg.com; " .
            "font-src 'self' data: https://fonts.bunny.net; " .
            "img-src 'self' data: https://fluxui.dev; " .
            "connect-src 'self' https://www.google.com https://www.gstatic.com; " .
            "base-uri 'self'; " .
            "frame-ancestors 'none'; " .
            "frame-src 'self' https://www.google.com; " .
            "form-action 'self';";

        $response->headers->set('Content-Security-Policy', $csp, true);

        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(self)',
            true
        );
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', true);

        return $response;
    }
}
