<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAuthenticatedAdmin = (Auth::check());

        // This will be excecuted if the new authentication fails.
        if (! $isAuthenticatedAdmin) {
            return redirect('/')->with('message', __('messages.authentication_error'));
        }

        return $next($request);
    }
}
