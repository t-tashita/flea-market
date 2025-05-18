<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAddressIncomplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (
            $user &&
            (
                empty($user->user_postal_code) ||
                empty($user->user_address) ||
                empty($user->user_building)
            ) &&
            !$request->is('mypage/profile') &&
            !$request->is('logout') // 無限ループ回避
        ) {
            return redirect()->route('profile.show');
        }

        return $next($request);
    }
}
