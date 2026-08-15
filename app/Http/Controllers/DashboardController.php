<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        $stats = [
            'total_surat'    => Surat::count(),
            'total_pengguna' => User::count(),
            'total_role'     => Role::count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $aktivitasTerbaru = Surat::with(['template', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.super-admin', compact('stats', 'aktivitasTerbaru'));
    }

    public function admin()
    {
        $stats = [
            'total_surat'    => Surat::count(),
            'total_pengguna' => User::count(),
            'total_role'     => Role::count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $aktivitasTerbaru = Surat::with(['template', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'aktivitasTerbaru'));
    }

    public function user()
    {
        $userId = Auth::id();

        $stats = [
            'surat_saya' => Surat::where('user_id', $userId)->count(),
        ];

        $riwayatTerakhir = Surat::where('user_id', $userId)
            ->with('template')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('stats', 'riwayatTerakhir'));
    }
}
