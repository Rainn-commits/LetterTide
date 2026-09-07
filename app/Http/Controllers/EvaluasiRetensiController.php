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

        $namaRole = strtolower($user->role->nama_role ?? $user->role);

        if ($namaRole === 'user') {
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

    public function simpanPenilaian(Request $request, Surat $surat)
    {
        $user = Auth::user();

        if ($user->role === 'user' && $surat->user_id !== $user->id) { // sesuaikan nama kolom pemilik (user_id / created_by)
            abort(403, 'Anda tidak berhak menilai surat milik pengguna lain.');
        }

        $validated = $request->validate([
            'nilai_guna'   => ['required', 'in:Hukum,Keuangan,Operasional,Pemberitahuan'],
            'kerahasiaan'  => ['required', 'in:Sangat Rahasia,Rahasia,Biasa/Terbuka'],
        ]);

        EvaluasiRetensi::updateOrCreate(
            ['surat_id' => $surat->id],
            $validated
        );

        return back()->with('success', "Penilaian untuk surat \"{$surat->nomor_surat}\" tersimpan.");
    }

    public function jalankanEvaluasi(RetensiScoringService $service)
    {
        $jumlahDievaluasi = $service->evaluasiMassal();

        if ($jumlahDievaluasi === 0) {
            return back()->withErrors(['evaluasi' => 'Belum ada surat yang diberi penilaian nilai guna & kerahasiaan.']);
        }

        return back()->with('success', "Evaluasi retensi berhasil dijalankan untuk {$jumlahDievaluasi} surat.");
    }

    public function usulkan(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'usulan_user' => ['required', 'in:Musnahkan,Permanen,Diperpanjang'],
            'catatan_user' => ['nullable', 'string', 'max:255'],
        ]);

        EvaluasiRetensi::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'usulan_user' => $validated['usulan_user'],
                'catatan_user' => $validated['catatan_user'] ?? null,
                'status_persetujuan' => 'Pending', // Menunggu eksekusi admin
            ]
        );

        return back()->with('success', "Usulan retensi untuk surat \"{$surat->nomor_surat}\" berhasil dikirim ke Admin.");
    }

    public function approve(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'status_persetujuan' => ['required', 'in:Disetujui,Ditolak'],
            'tindakan_akhir' => ['required', 'in:Musnahkan,Inaktif,Permanen'],
        ]);

        EvaluasiRetensi::updateOrCreate(
            ['surat_id' => $surat->id],
            [
                'status_persetujuan' => $validated['status_persetujuan'],
                'tindakan_akhir' => $validated['tindakan_akhir'],
                'eksekutor_id' => auth::id(),
            ]
        );

        return back()->with('success', "Status retensi surat \"{$surat->nomor_surat}\" berhasil diperbarui.");
    }
}
