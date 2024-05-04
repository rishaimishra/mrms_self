<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {

            if ($this->auth->guard($guard)->check() && $this->auth->guard($guard)->user()->isActive()) {
                return $this->auth->shouldUse($guard);
            }
        }

        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        $path = explode('/', $request->path());

        if (! $request->expectsJson()) {

            if($path[0] == AdminUser::ADMIN_PATH)
            {
                if($request->user('admin'))
                {
                    Auth::guard('admin')->logout();
                    $request->session()->invalidate();
                }

                return route('admin.auth.login');
            }

            return route('login');
        }
    }
}
