<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $lintasUnit = $user->isGlobal();

        $query = LogAktivitas::with('user');

        if (!$lintasUnit) {
            $idSuperAdmin = User::whereHas('role.permissions', function ($q) {
                $q->where('kode_permission', 'role.manage');
            })->pluck('id');

            $query->where(function ($q) use ($idSuperAdmin) {
                $q->whereNotIn('modul', ['Role', 'Pengguna'])
                ->orWhereNotIn('user_id', $idSuperAdmin);
            });
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

        if (!$lintasUnit) {
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
