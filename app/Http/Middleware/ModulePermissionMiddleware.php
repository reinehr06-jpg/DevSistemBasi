<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModulePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $permission = "{$module}.{$action}";
        
        if (!$request->user()->hasPermission($permission)) {
            abort(403, "Você não tem permissão para {$action} {$module}.");
        }

        return $next($request);
    }
}