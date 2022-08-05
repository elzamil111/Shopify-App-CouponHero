<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\EncryptException;

class AuthenticatePortal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            decrypt($request->input('portal_jwt'));
        } catch (EncryptException $e) {
            abort(401, 'Authentication Failure');
        }

        return $next($request);
    }
}
