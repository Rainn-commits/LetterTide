<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Surat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $folder = $request->input('folder', 'keluar');
        if (!in_array($folder, ['masuk', 'keluar'])) {
            $folder = 'keluar';
        }

        $jenis = $folder === 'masuk' ? 'Masuk' : 'Keluar';

        $query = Surat::with(['template', 'user', 'kopSurat'])
            ->where('jenis', $jenis)
            ->latest('tanggal_surat');

        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->input('pembuat') === 'saya') {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('template', function ($qt) use ($search) {
                      $qt->where('nama_template', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
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

        if ($request->filled('status_arsip')) {
            if ($request->input('status_arsip') === 'uploaded') {
                $query->whereNotNull('file_arsip');
            } elseif ($request->input('status_arsip') === 'pending') {
                $query->whereNull('file_arsip');
            }
        }

        $suratList = $query->paginate(10)->withQueryString();

        $pejabatList = User::orderBy('name')->get();
        $templates = TemplateSurat::where('is_aktif', true)
            ->orderBy('nama_template')
            ->get();

        return view('surat.arsip', compact('suratList', 'pejabatList', 'folder', 'templates'));
    }
}
