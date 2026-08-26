<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAktivitasController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !in_array($user->role_id, [1, 2])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $logs = LogAktivitas::with('user')
            ->latest('created_at')
            ->paginate(20);

        return view('log-aktivitas.index', compact('logs'));
    }
}
