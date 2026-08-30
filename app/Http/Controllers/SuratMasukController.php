<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratMasukController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat'   => ['required', 'string', 'max:50'],
            'pengirim'      => ['required', 'string', 'max:150'],
            'perihal'       => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'file_arsip'    => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $path = $request->file('file_arsip')->store('arsip_surat', 'public');

        $surat = Surat::create([
            'user_id'       => Auth::id(),
            'nomor_surat'   => $validated['nomor_surat'],
            'jenis'         => 'Masuk',
            'pengirim'      => $validated['pengirim'],
            'perihal'       => $validated['perihal'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'file_arsip'    => $path,
        ]);

        LogAktivitas::catat(
            'Membuat',
            'Surat Masuk',
            "Mengarsipkan surat masuk \"{$surat->nomor_surat}\" dari \"{$surat->pengirim}\""
        );

        return redirect()->route('arsip.index', ['folder' => 'masuk'])
            ->with('success', "Surat masuk \"{$surat->nomor_surat}\" berhasil diarsipkan.");
    }
}
