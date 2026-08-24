<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiRetensi;
use App\Models\Surat;
use App\Services\RetensiScoringService;
use Illuminate\Http\Request;

class EvaluasiRetensiController extends Controller
{
    public function index()
    {
        $arsip = Surat::where('jenis', 'Masuk')
            ->with('evaluasiRetensi', 'template')
            ->orderByDesc('tanggal_surat')
            ->get();

        return view('evaluasi-retensi.index', compact('arsip'));
    }

    public function simpanPenilaian(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'nilai_guna'   => ['required', 'in:Hukum,Keuangan,Operasional,Pemberitahuan'],
            'kerahasiaan'  => ['required', 'in:Sangat Rahasia,Rahasia,Biasa/Terbuka'],
        ]);

        EvaluasiRetensi::updateOrCreate(
            ['surat_id' => $surat->id],
            $validated
        );

        return back()->with('success', "Penilaian untuk surat \"{$surat->nomor_surat}\" tersimpan. Jalankan evaluasi untuk melihat hasil.");
    }

    public function jalankanEvaluasi(RetensiScoringService $service)
    {
        $jumlahDievaluasi = $service->evaluasiMassal();

        if ($jumlahDievaluasi === 0) {
            return back()->withErrors(['evaluasi' => 'Belum ada surat yang diberi penilaian nilai guna & kerahasiaan.']);
        }

        return back()->with('success', "Evaluasi retensi berhasil dijalankan untuk {$jumlahDievaluasi} surat.");
    }
}
