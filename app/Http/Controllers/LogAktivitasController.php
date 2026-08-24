<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index()
    {
        $logs = LogAktivitas::with('user')
            ->latest('created_at')
            ->paginate(20);

        return view('log-aktivitas.index', compact('logs'));
    }
}
