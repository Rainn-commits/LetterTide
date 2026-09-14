<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !in_array($user->role_id, [1, 2])) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $roleName = $user->role?->nama_role ?? '';

        $query = LogAktivitas::with('user');

        if ($roleName !== 'Super Admin') {
            $query->where('modul', '!=', 'Role');
        }

        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('aksi', 'like', "%{$search}%")
                ->orWhere('modul', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->sort === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $aksiList = LogAktivitas::select('aksi')
            ->distinct()
            ->orderBy('aksi')
            ->pluck('aksi');

        $modulQuery = LogAktivitas::query();

        if ($roleName !== 'Super Admin') {
            $modulQuery->where('modul', '!=', 'Role');
        }

        $modulList = $modulQuery
            ->select('modul')
            ->distinct()
            ->orderBy('modul')
            ->pluck('modul');

        $logs = $query->paginate(20)->withQueryString();

        return view('log-aktivitas.index', compact(
            'logs',
            'aksiList',
            'modulList'
        ));
    }
}

