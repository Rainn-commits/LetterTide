<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RetensiKriteria;
use Illuminate\Http\Request;

class RetensiKriteriaController extends Controller
{
    public function index()
    {
        $kriteria = RetensiKriteria::orderBy('kode')->get();

        return view('retensi-kriteria.index', compact('kriteria'));
    }

    public function update(Request $request, RetensiKriteria $retensiKriterion)
    {
        $validated = $request->validate([
            'bobot' => ['required', 'numeric', 'min:0', 'max:1'],
        ]);

        $retensiKriterion->update(['bobot' => $validated['bobot']]);

        $totalBobot = RetensiKriteria::sum('bobot');

        return back()->with(
            'success',
            "Bobot {$retensiKriterion->kode} berhasil diperbarui. Total bobot saat ini: " . round($totalBobot, 2) .
            ($totalBobot != 1 ? ' (disarankan totalnya = 1.0 agar skor akurat)' : '')
        );
    }
}
