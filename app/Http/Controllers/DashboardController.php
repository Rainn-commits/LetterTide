<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema; // <-- Tambahkan ini
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        // Cek apakah tabel 'surats' & 'roles' sudah dibuat di DB
        $hasSuratTable = Schema::hasTable('surats');
        $hasRoleTable  = Schema::hasTable('roles');

        $stats = [
            'total_surat'     => $hasSuratTable ? Surat::count() : 0,
            'total_pengguna'  => User::count(),
            'total_role'      => $hasRoleTable ? Role::count() : 0,
            'surat_bulan_ini' => $hasSuratTable 
                ? Surat::whereMonth('tanggal_surat', now()->month)
                    ->whereYear('tanggal_surat', now()->year)
                    ->count() 
                : 0,
        ];

        $aktivitasTerbaru = $hasSuratTable 
            ? Surat::with(['template', 'user'])->latest()->limit(5)->get()
            : collect(); // Kirim collection kosong biar enggak error

        return view('dashboard.super-admin', compact('stats', 'aktivitasTerbaru'));
    }

    public function admin()
    {
        $hasSuratTable = Schema::hasTable('surats');
        $hasRoleTable  = Schema::hasTable('roles');

        $stats = [
            'total_surat'     => $hasSuratTable ? Surat::count() : 0,
            'total_pengguna'  => User::count(),
            'total_role'      => $hasRoleTable ? Role::count() : 0,
            'surat_bulan_ini' => $hasSuratTable 
                ? Surat::whereMonth('tanggal_surat', now()->month)
                    ->whereYear('tanggal_surat', now()->year)
                    ->count() 
                : 0,
        ];

        $aktivitasTerbaru = $hasSuratTable 
            ? Surat::with(['template', 'user'])->latest()->limit(5)->get()
            : collect();

        return view('dashboard.admin', compact('stats', 'aktivitasTerbaru'));
    }

    public function user()
    {
        $userId = Auth::id();
        $hasSuratTable = Schema::hasTable('surats');

        $stats = [
            'surat_saya' => $hasSuratTable ? Surat::where('user_id', $userId)->count() : 0,
        ];

        $riwayatTerakhir = $hasSuratTable 
            ? Surat::where('user_id', $userId)->with('template')->latest()->limit(5)->get()
            : collect();

        return view('dashboard.user', compact('stats', 'riwayatTerakhir'));
    }
}