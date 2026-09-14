<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiRetensi;
use App\Models\Surat;
use App\Models\TemplateSurat;
use App\Models\User;
use App\Services\RetensiScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluasiRetensiController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $namaRole = $user->role->nama_role ?? '';
        $isPrivileged = in_array($namaRole, ['Super Admin', 'Admin']);

        $query = Surat::with('evaluasiRetensi', 'template', 'user');

        if (!$isPrivileged) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($qq) use ($search, $isPrivileged) {
                $qq->where('nomor_surat', 'like', "%{$search}%")
                   ->orWhereHas('template', function ($qt) use ($search) {
                       $qt->where('nama_template', 'like', "%{$search}%");
                   });

                if ($isPrivileged) {
                    $qq->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_surat', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_surat', '<=', $request->input('tanggal_selesai'));
        }

        if ($request->filled('template_id')) {
            $query->where('template_id', $request->input('template_id'));
        }

        if ($isPrivileged && $request->filled('status_evaluasi')) {
            $status = $request->input('status_evaluasi');

            if ($status === 'belum') {
                $query->whereDoesntHave('evaluasiRetensi', function ($qe) {
                    $qe->whereNotNull('status_rekomendasi');
                });
            } elseif ($status === 'menunggu') {
                $query->whereHas('evaluasiRetensi', function ($qe) {
                    $qe->whereNotNull('status_rekomendasi')
                       ->where(function ($qs) {
                           $qs->whereNull('status_persetujuan')
                              ->orWhere('status_persetujuan', '!=', 'Disetujui');
                       });
                });
            } elseif ($status === 'tervalidasi') {
                $query->whereHas('evaluasiRetensi', function ($qe) {
                    $qe->where('status_persetujuan', 'Disetujui');
                });
            }
        }

        $sort = $request->input('sort', 'tanggal_terbaru');

        if ($sort === 'skor_tertinggi' && $isPrivileged) {
            $query->leftJoin('evaluasi_retensi', 'evaluasi_retensi.surat_id', '=', 'surat.id')
                  ->orderByDesc('evaluasi_retensi.skor_total')
                  ->select('surat.*');
        } elseif ($sort === 'skor_terendah' && $isPrivileged) {
            $query->leftJoin('evaluasi_retensi', 'evaluasi_retensi.surat_id', '=', 'surat.id')
                  ->orderBy('evaluasi_retensi.skor_total')
                  ->select('surat.*');
        } elseif ($sort === 'tanggal_terlama') {
            $query->orderBy('tanggal_surat', 'asc');
        } elseif ($sort === 'akses_terbanyak') {
            $query->orderByDesc('jumlah_akses');
        } else {
            $sort = 'tanggal_terbaru';
            $query->orderByDesc('tanggal_surat');
        }

        $arsip = $query->paginate(20)->withQueryString();

        $templates = TemplateSurat::orderBy('nama_template')->get();

        return view('evaluasi-retensi.index', compact('arsip', 'templates', 'sort'));
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
