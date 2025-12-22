<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\App as LaravelApp;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectUsersTo(AppServiceProvider::HOME);
        $middleware->redirectGuestsTo('/');

        // Trust Proxies (Host Header Injection Protection)
        // Only trust specific proxy IPs to prevent HTTP header injection
        // Add your load balancer IPs here (e.g., AWS LB IPs, Cloudflare)
        $middleware->trustProxies(at: [
            '*'
        ]);

        $middleware->web([
            App\Http\Middleware\HttpResponseHeaders::class,  // Security headers FIRST
            // App\Http\Middleware\HostVerificationMiddleware::class, // Temporarily disabled for development
            App\Http\Middleware\VerifyCsrfToken::class,
            Illuminate\Session\Middleware\AuthenticateSession::class,
            App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        if (LaravelApp::environment('production')) {
            $exceptions->render(function (Throwable $e) {
                // In production, mask the actual error for 500s unless explicitly logged
                if ($e instanceof \Illuminate\Database\QueryException) {
                    return response()->json([
                        'message' => 'Server Error',
                    ], 500);
                }
                return null; // Let Laravel handle other exceptions normally (which are safe in prod)
            });
        }
    })->create();
