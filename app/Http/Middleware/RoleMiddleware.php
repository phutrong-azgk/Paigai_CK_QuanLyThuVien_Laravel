<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $currentRole = session('role');
        $user = session('library_user');

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['loi' => 'Bạn chưa đăng nhập.'], 401);
            }
            return redirect()->route('login');
        }

        if (!empty($roles) && !in_array($currentRole, $roles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['loi' => 'Bạn không có quyền thực hiện thao tác này.'], 403);
            }
            return abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }
}