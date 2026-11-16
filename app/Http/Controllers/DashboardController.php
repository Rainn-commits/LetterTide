<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private const PERMISSION_DASHBOARD_ADMIN = ['arsip.unit', 'pengguna.unit'];

    private function getTrafficData($userId = null)
    {
        $trafficData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->locale('id')->translatedFormat('M');

            $query = Surat::whereYear('tanggal_surat', $date->year)
                ->whereMonth('tanggal_surat', $date->month);

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $trafficData[$monthName] = (int) $query->count();
        }

        return $trafficData;
    }

    public function superAdmin()
    {
        abort_unless($this->saya()->isGlobal(), 403, 'Anda tidak memiliki akses ke dashboard ini.');

        $stats = [
            'total_surat'     => Surat::count(),
            'total_pengguna'  => User::count(),
            'total_role'      => Role::count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $trafficData = $this->getTrafficData();
        $aktivitasTerbaru = Surat::with(['template', 'user'])->latest()->limit(5)->get();

        return view('dashboard.super-admin', compact('stats', 'aktivitasTerbaru', 'trafficData'));
    }

    public function admin()
    {
        abort_unless(
            $this->saya()->punyaSalahSatuPermission(self::PERMISSION_DASHBOARD_ADMIN),
            403,
            'Anda tidak memiliki akses ke dashboard ini.'
        );

        $stats = [
            'total_surat'     => Surat::count(),
            'total_pengguna'  => User::diUnitSaya()->count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $trafficData = $this->getTrafficData();
        $aktivitasTerbaru = Surat::with(['template', 'user'])->latest()->limit(5)->get();

        return view('dashboard.admin', compact('stats', 'aktivitasTerbaru', 'trafficData'));
    }

    public function user()
    {
        $userId = Auth::id();

        $stats = [
            'surat_saya' => Surat::where('user_id', $userId)->count(),
        ];

        $trafficData = $this->getTrafficData($userId);
        $riwayatTerakhir = Surat::where('user_id', $userId)->with('template')->latest()->limit(5)->get();

        return view('dashboard.user', compact('stats', 'riwayatTerakhir', 'trafficData'));
    }

    public function index()
    {
        $user = $this->saya();

        if ($user->isGlobal()) {
            return $this->superAdmin();
        }

        if ($user->punyaSalahSatuPermission(self::PERMISSION_DASHBOARD_ADMIN)) {
            return $this->admin();
        }

        return $this->user();
    }

    private function saya(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
