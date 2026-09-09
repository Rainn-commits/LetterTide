<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiRetensi;
use App\Models\Surat;
use App\Models\User;
use App\Services\RetensiScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiRetensiController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $namaRole = $user->role->nama_role ?? '';

        if (strtolower($namaRole) === 'user') {
            $arsip = Surat::with('evaluasiRetensi', 'template', 'user')
                    ->where('user_id', $user->id)
                    ->orderByDesc('tanggal_surat')
                    ->paginate(20);
        } else {
            $arsip = Surat::with('evaluasiRetensi', 'template', 'user')
                    ->orderByDesc('tanggal_surat')
                    ->paginate(20);
        }

        return view('evaluasi-retensi.index', compact('arsip'));
    }

    public function simpanPenilaian(Request $request, Surat $surat, RetensiScoringService $service)
    {
        $user = Auth::user();
        $namaRole = $user->role->nama_role ?? '';
        $isAdmin = in_array($namaRole, ['Super Admin', 'Admin']);

        if (strtolower($namaRole) === 'user' && $surat->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak menilai surat milik pengguna lain.');
        }

        $validated = $request->validate([
            'nilai_guna'  => ['required', 'in:Hukum,Keuangan,Operasional,Pemberitahuan'],
            'kerahasiaan' => ['required', 'in:Sangat Rahasia,Rahasia,Biasa/Terbuka'],
        ]);

        $dataToSave = array_merge($validated, [
            'status_persetujuan' => $isAdmin ? 'Disetujui' : 'Menunggu',
            'divalidasi_oleh'    => $isAdmin ? $user->id : null,
            'divalidasi_pada'    => $isAdmin ? now() : null,
        ]);

        EvaluasiRetensi::updateOrCreate(
            ['surat_id' => $surat->id],
            $dataToSave
        );

        $service->evaluasiMassal();

        return back()->with('success', "Penilaian untuk surat \"{$surat->nomor_surat}\" tersimpan dan skor retensi diperbarui.");
    }

    public function validasi(Surat $surat)
    {
        /** @var User $user */
        $user = Auth::user();
        $namaRole = $user->role->nama_role ?? '';

        if (!in_array($namaRole, ['Super Admin', 'Admin'])) {
            abort(403, 'Hanya Admin/Super Admin yang dapat memvalidasi status retensi.');
        }

        $eval = $surat->evaluasiRetensi;

        if (!$eval || !$eval->status_rekomendasi) {
            return back()->withErrors(['evaluasi' => 'Surat ini belum memiliki skor retensi untuk divalidasi.']);
        }

        $eval->update([
            'status_persetujuan' => 'Disetujui',
            'divalidasi_oleh'    => $user->id,
            'divalidasi_pada'    => now(),
        ]);

        return back()->with('success', "Status retensi surat \"{$surat->nomor_surat}\" telah divalidasi.");
    }
}
