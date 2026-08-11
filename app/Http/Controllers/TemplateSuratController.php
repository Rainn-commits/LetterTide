<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use App\Http\Controllers\Controller;
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

        TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => 1,
            'is_aktif'      => true,
        ]);

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

        TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => $templateSurat->versi + 1,
            'is_aktif'      => true,
        ]);

        return redirect()->route('template-surat.index')
            ->with('success', 'Template berhasil disimpan sebagai versi baru.');
    }

    public function destroy(TemplateSurat $templateSurat)
    {
        $templateSurat->update(['is_aktif' => false]);

        return redirect()->route('template-surat.index')
            ->with('success', 'Template dinonaktifkan.');
    }
}
