<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check()){
            switch (Auth::user()->type_id){
                case 1: return redirect('admin'); break;
                case 2: return redirect('user1'); break;
                case 3: return redirect('user2'); break;
                case 4: return redirect('user3'); break;
                case 5: return redirect('user4'); break;
                case 6: return redirect('user5'); break;
                case 7: return redirect('user6'); break;
                case 8: return redirect('user7'); break;
            }
        }
        return $next($request);

    }
}
