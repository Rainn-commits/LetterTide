<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$kodePermission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $punyaAkses = $user->role->permissions()
            ->whereIn('kode_permission', $kodePermission)
            ->exists();

        if (!$punyaAkses) {
            abort(403, 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        return $next($request);
    }
}
