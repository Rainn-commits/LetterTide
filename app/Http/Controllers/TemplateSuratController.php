<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use App\Models\PengaturanKopSurat;
use App\Models\LogAktivitas;
use App\Http\Controllers\Controller;
use App\Services\PlaceholderParser;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $templates = TemplateSurat::with('unitKerja')
            ->orderBy('nama_template')
            ->orderByDesc('versi')
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
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
        ], [
            'unit_kerja_id.required' => 'Unit kerja pemilik template wajib dipilih.',
        ]);

        $template = TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => 1,
            'is_aktif'      => true,
            'unit_kerja_id' => $validated['unit_kerja_id'],
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
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
        ], [
            'unit_kerja_id.required' => 'Unit kerja pemilik template wajib dipilih.',
        ]);

        TemplateSurat::where('nama_template', $templateSurat->nama_template)
            ->update(['unit_kerja_id' => $validated['unit_kerja_id']]);

        $templateSurat->update(['is_aktif' => false]);

        $baru = TemplateSurat::create([
            'nama_template' => $validated['nama_template'],
            'isi_template'  => $validated['isi_template'],
            'versi'         => $templateSurat->versi + 1,
            'is_aktif'      => true,
            'unit_kerja_id' => $validated['unit_kerja_id'],
        ]);

        LogAktivitas::catat('Mengubah', 'Template Surat', "Memperbarui template \"{$baru->nama_template}\" ke versi {$baru->versi}");

        return redirect()->route('template-surat.index')
            ->with('success', 'Template berhasil disimpan sebagai versi baru.');
    }

    public function destroy(TemplateSurat $templateSurat)
    {
        $nama = $templateSurat->nama_template;
        $versi = $templateSurat->versi;

        if ($templateSurat->surat()->exists()) {
            return redirect()->route('template-surat.index')
                ->with('error', "Template \"{$nama}\" versi {$versi} tidak bisa dihapus karena sudah dipakai oleh surat.");
        }

        $templateSurat->delete();

        LogAktivitas::catat(
            'Menghapus',
            'Template Surat',
            "Menghapus template \"{$nama}\" versi {$versi}"
        );

        return redirect()->route('template-surat.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    public function parsePreview(Request $request)
    {
        $text = $request->input('isi_template', '');
        $placeholders = PlaceholderParser::extract($text);

        return response()->json(['placeholders' => $placeholders]);
    }

    public function preview(Request $request, TemplateSurat $templateSurat)
    {
        $daftarKop = PengaturanKopSurat::where('is_aktif', true)
            ->when($templateSurat->unit_kerja_id, fn ($q) => $q->where('unit_kerja_id', $templateSurat->unit_kerja_id))
            ->orderBy('nama_kop')
            ->orderByDesc('versi')
            ->get();

        $kopId = $request->integer('kop_id') ?: null;

        $kopSurat = $kopId ? $daftarKop->firstWhere('id', $kopId) : null;
        $kopSurat ??= $daftarKop->sortByDesc('id')->first();

        $nilaiContoh = [];
        foreach (PlaceholderParser::extract($templateSurat->isi_template) as $nama) {
            $nilaiContoh[$nama] = '<span class="ph-preview">' . e($nama) . '</span>';
        }

        $isiRender = PlaceholderParser::render($templateSurat->isi_template, $nilaiContoh);

        $isiUtama = $isiRender;
        $isiLampiran = null;

        if (stripos($isiRender, 'page-break-before: always') !== false) {
            $bagian = preg_split(
                '/<div[^>]*style=["\'][^"\']*page-break-before:\s*always[^"\']*["\'][^>]*><\/div>/i',
                $isiRender,
                2
            );

            if (count($bagian) === 2) {
                [$isiUtama, $isiLampiran] = $bagian;
            }
        }

        return view('template-surat.preview', [
            'template'             => $templateSurat,
            'daftarKop'            => $daftarKop,
            'kopSurat'             => $kopSurat,
            'isiUtama'             => $isiUtama,
            'isiLampiran'          => $isiLampiran,
            'jabatanPenandatangan' => 'Direktur PT. Inovindo Digital Media',
            'namaPenandatangan'    => 'Novi Setia Nurviat',
        ]);
    }
}
