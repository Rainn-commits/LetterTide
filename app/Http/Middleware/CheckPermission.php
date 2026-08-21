<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $kodePermission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $punyaAkses = $user->role->permissions()
            ->where('kode_permission', $kodePermission)
            ->exists();

        if (!$punyaAkses) {
            abort(403, 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        return $next($request);
    }
}