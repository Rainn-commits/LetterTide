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
    private function getLineChartData()
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->locale('id')->translatedFormat('M Y');

            $count = Surat::whereYear('tanggal_surat', $date->year)
                ->whereMonth('tanggal_surat', $date->month)
                ->count();

            $data[] = $count;
        }

        $hasData = array_sum($data) > 0;

        return compact('labels', 'data', 'hasData');
    }

    private function getUserPieChartData($userId)
    {
        $suratPerTemplate = Surat::where('user_id', $userId)
            ->with('template')
            ->get()
            ->groupBy('template.nama_template');

        $labels = [];
        $data = [];

        foreach ($suratPerTemplate as $namaTemplate => $items) {
            $labels[] = $namaTemplate ?: 'Tanpa Template';
            $data[] = $items->count();
        }

        $hasData = count($data) > 0;

        return compact('labels', 'data', 'hasData');
    }

    public function superAdmin()
    {
        $stats = [
            'total_surat'     => Surat::count(),
            'total_pengguna'  => User::count(),
            'total_role'      => Role::count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $trafficData = $this->getLineChartData();
        $aktivitasTerbaru = Surat::with(['template', 'user'])->latest()->limit(5)->get();

        return view('dashboard.super-admin', compact('stats', 'aktivitasTerbaru', 'trafficData'));
    }

    public function admin()
    {
        $stats = [
            'total_surat'     => Surat::count(),
            'total_pengguna'  => User::count(),
            'total_role'      => Role::count(),
            'surat_bulan_ini' => Surat::whereMonth('tanggal_surat', now()->month)
                ->whereYear('tanggal_surat', now()->year)
                ->count(),
        ];

        $trafficData = $this->getLineChartData();
        $aktivitasTerbaru = Surat::with(['template', 'user'])->latest()->limit(5)->get();

        return view('dashboard.admin', compact('stats', 'aktivitasTerbaru', 'trafficData'));
    }

    public function user()
    {
        $userId = Auth::id();

        $stats = [
            'surat_saya' => Surat::where('user_id', $userId)->count(),
        ];

        $trafficData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $monthName = $date->locale('id')->translatedFormat('M');
            $trafficData[$monthName] = Surat::where('user_id', $userId)
                ->whereYear('tanggal_surat', $date->year)
                ->whereMonth('tanggal_surat', $date->month)
                ->count();
        }

        $riwayatTerakhir = Surat::where('user_id', $userId)
            ->with('template')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('stats', 'riwayatTerakhir', 'trafficData'));
    }
}