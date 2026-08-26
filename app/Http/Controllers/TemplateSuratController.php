<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use App\Models\LogAktivitas;
use App\Http\Controllers\Controller;
use App\Services\PlaceholderParser;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $templates = TemplateSurat::where('is_aktif', true)
            ->orderBy('nama_template')
            ->get();

        return view('template-surat.index', compact('templates'));
    }

    public function create()
    {
        return view('template-surat.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_template' => ['required', 'string', 'max:100'],
            'isi_template'  => ['required', 'string'],
        ]);

        $template = TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => 1,
            'is_aktif'      => true,
        ]);

        LogAktivitas::catat('Membuat', 'Template Surat', "Membuat template \"{$template->nama_template}\" versi 1");

        return redirect()->route('template-surat.index')
            ->with('success', 'Template berhasil disimpan.');
    }

    public function edit(TemplateSurat $templateSurat)
    {
        return view('template-surat.edit', compact('templateSurat'));
    }

    public function update(Request $request, TemplateSurat $templateSurat)
    {
        $validated = $request->validate([
            'nama_template' => ['required', 'string', 'max:100'],
            'isi_template'  => ['required', 'string'],
        ]);

        $templateSurat->update(['is_aktif' => false]);

        $baru = TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => $templateSurat->versi + 1,
            'is_aktif'      => true,
        ]);

        LogAktivitas::catat('Mengubah', 'Template Surat', "Memperbarui template \"{$baru->nama_template}\" ke versi {$baru->versi}");

        return redirect()->route('template-surat.index')
            ->with('success', 'Template berhasil disimpan sebagai versi baru.');
    }

    public function destroy(TemplateSurat $templateSurat)
    {
        $nama = $templateSurat->nama_template;
        $templateSurat->update(['is_aktif' => false]);

        LogAktivitas::catat('Menghapus', 'Template Surat', "Menonaktifkan template \"{$nama}\" versi {$templateSurat->versi}");

        return redirect()->route('template-surat.index')
            ->with('success', 'Template dinonaktifkan.');
    }

    public function parsePreview(Request $request)
    {
        $text = $request->input('isi_template', '');
        $placeholders = PlaceholderParser::extract($text);

        return response()->json(['placeholders' => $placeholders]);
    }
}
