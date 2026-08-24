<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKopSurat;
use App\Models\LogAktivitas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KopSuratController extends Controller
{
    public function index()
    {
        $kopSuratAktif = PengaturanKopSurat::where('is_aktif', true)->first();

        $riwayat = PengaturanKopSurat::where('is_aktif', false)
            ->orderByDesc('versi')
            ->get();

        return view('kop-surat.index', compact('kopSuratAktif', 'riwayat'));
    }

    public function create()
    {
        $kopSuratAktif = PengaturanKopSurat::where('is_aktif', true)->first();

        return view('kop-surat.create', compact('kopSuratAktif'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo'      => ['nullable', 'image', 'max:2048'],
            'nama_pt'   => ['required', 'string', 'max:100'],
            'alamat_pt' => ['required', 'string', 'max:255'],
            'telepon'   => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:100'],
            'website'   => ['nullable', 'string', 'max:100'],
        ]);

        $kopSuratLama = PengaturanKopSurat::where('is_aktif', true)->first();
        $versiBaru = $kopSuratLama ? $kopSuratLama->versi + 1 : 1;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('kop-surat', 'public');
        } else {
            $logoPath = $kopSuratLama->logo ?? null;
        }

        if ($kopSuratLama) {
            $kopSuratLama->update(['is_aktif' => false]);
        }

        PengaturanKopSurat::create([
            'logo'      => $logoPath,
            'nama_pt'   => $validated['nama_pt'],
            'alamat_pt' => $validated['alamat_pt'],
            'telepon'   => $validated['telepon'],
            'email'     => $validated['email'],
            'website'   => $validated['website'] ?? null,
            'versi'     => $versiBaru,
            'is_aktif'  => true,
            'created_at' => now(),
        ]);

        LogAktivitas::catat('Mengubah', 'Kop Surat', "Menyimpan kop surat sebagai versi {$versiBaru}");
        
        return redirect()->route('kop-surat.index')
            ->with('success', "Kop surat berhasil disimpan sebagai versi {$versiBaru}.");
    }
}
