<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * الاستخدام: ->middleware('role:farmer') أو ->middleware('role:admin,admin_super')
 * بدون وسائط: يتحقق فقط أن الحساب Active وغير محظور (يمسك حالة التعليق بعد إصدار التوكن).
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->isActive()) {
            return response()->json(['status' => 403, 'message' => 'Account is not active'], 403);
        }

        if ($roles && !in_array($user->role->value, $roles, true)) {
            return response()->json(['status' => 403, 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
