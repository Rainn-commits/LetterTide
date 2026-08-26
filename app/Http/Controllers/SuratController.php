<?php

namespace App\Http\Controllers;

use App\Models\IsiSurat;
use App\Models\LogAktivitas;
use App\Models\PengaturanKopSurat;
use App\Models\Surat;
use App\Models\TemplateSurat;
use App\Models\User;
use App\Services\PlaceholderParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuratController extends Controller
{
    public function create()
    {
        $templates = TemplateSurat::where('is_aktif', true)
            ->orderBy('nama_template')
            ->get();

        return view('surat.pilih-template', compact('templates'));
    }

    public function form(TemplateSurat $template)
    {
        $fields = PlaceholderParser::toFormFields($template->isi_template);
        $users = User::orderBy('name')->get();
        $kopSurats = PengaturanKopSurat::orderBy('created_at', 'desc')->get();

        return view('surat.form', compact('template', 'fields', 'users', 'kopSurats'));
    }

    public function store(Request $request, TemplateSurat $template)
    {
        $validated = $request->validate([
            'kop_surat_id' => ['required', 'exists:pengaturan_kop_surats,id'],
            'lampiran'     => ['nullable', 'string'],
            'fields'       => ['required', 'array'],
            'fields.*'     => ['required', 'string'],
        ]);

        $nomorSurat = $this->generateNomorSurat($template);

        $surat = Surat::create([
            'template_id'   => $template->id,
            'user_id'       => Auth::id(),
            'kop_surat_id'  => $validated['kop_surat_id'],
            'nomor_surat'   => $nomorSurat,
            'lampiran'      => $request->input('lampiran', '-'),
            'jenis'         => 'Keluar',
            'tanggal_surat' => now()->toDateString(),
        ]);

        foreach ($validated['fields'] as $namaField => $nilaiField) {
            IsiSurat::create([
                'surat_id'    => $surat->id,
                'nama_field'  => $namaField,
                'nilai_field' => $nilaiField,
                'created_at'  => now(),
            ]);
        }

        LogAktivitas::catat('Membuat', 'Surat', "Membuat surat \"{$nomorSurat}\" dari template \"{$template->nama_template}\"");

        return redirect()->route('surat.show', $surat)
            ->with('success', "Surat berhasil dibuat dengan nomor {$nomorSurat}.");
    }

    public function show(Surat $surat)
    {
        $surat->increment('jumlah_akses');
        $surat->load(['template', 'kopSurat', 'isiSurat', 'user']);

        $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();
        $isiSuratRendered = PlaceholderParser::render($surat->template->isi_template, $values);

        $namaPenandatangan = $values['nama_penandatangan'] 
                        ?? $values['Nama Penandatangan'] 
                        ?? $values['penandatangan'] 
                        ?? $surat->user?->name;

        $jabatanPenandatangan = $values['jabatan_penandatangan'] 
                            ?? $values['Jabatan Penandatangan'] 
                            ?? $values['jabatan'] 
                            ?? 'Direktur PT. Inovindo Digital Media';

        return view('surat.preview', compact(
            'surat', 
            'isiSuratRendered', 
            'namaPenandatangan', 
            'jabatanPenandatangan'
        ));
    }

    public function cetak(Surat $surat)
    {
        $surat->load(['template', 'kopSurat', 'isiSurat', 'user']);

        $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();
        $isiSuratRendered = PlaceholderParser::render($surat->template->isi_template, $values);

        $namaPenandatangan = $values['nama_penandatangan'] 
                        ?? $values['Nama Penandatangan'] 
                        ?? $values['penandatangan'] 
                        ?? $surat->user?->name;

        $jabatanPenandatangan = $values['jabatan_penandatangan'] 
                            ?? $values['Jabatan Penandatangan'] 
                            ?? $values['jabatan'] 
                            ?? 'Direktur PT. Inovindo Digital Media';

        return view('surat.cetak', compact('surat', 'isiSuratRendered', 'namaPenandatangan', 'jabatanPenandatangan'));
    }

    public function uploadArsip(Request $request, Surat $surat)
    {
        $request->validate([
            'file_arsip' => ['required', 'file', 'mimes:pdf,zip,rar', 'max:5120'],
        ]);

        if ($request->hasFile('file_arsip')) {
            if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
                Storage::disk('public')->delete($surat->file_arsip);
            }

            $path = $request->file('file_arsip')->store('arsip_surat', 'public');

            $surat->update([
                'file_arsip' => $path,
            ]);
        }

        return back()->with('success', 'File arsip surat berhasil diunggah.');
    }

    public function edit(Surat $surat)
    {
        $surat->load(['template', 'isiSurat']);
        $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();
        $template = $surat->template;
        $fields = PlaceholderParser::toFormFields($template->isi_template);
        $users = User::orderBy('name')->get();

        return view('surat.edit', compact('surat', 'template', 'fields', 'values', 'users'));
    }

    public function update(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'fields'   => ['required', 'array'],
            'fields.*' => ['required', 'string'],
        ]);

        foreach ($validated['fields'] as $namaField => $nilaiField) {
            IsiSurat::updateOrCreate(
                ['surat_id' => $surat->id, 'nama_field' => $namaField],
                ['nilai_field' => $nilaiField]
            );
        }

        LogAktivitas::catat('Mengubah', 'Surat', "Mengedit isi surat \"{$surat->nomor_surat}\"");

        return redirect()->route('surat.show', $surat)
            ->with('success', 'Isi surat berhasil diperbarui.');
    }

    private function generateNomorSurat(TemplateSurat $template): string
    {
        $tahunPenuh    = now()->year;
        $tahunSingkat  = now()->format('y');
        $runningNumber = Surat::whereYear('created_at', $tahunPenuh)->count() + 1;
        $nomorUrut     = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);
        $kodeTemplate  = $this->deriveKodeTemplate($template->nama_template);
        $bulanRomawi   = $this->bulanRomawi(now()->month);

        return "{$nomorUrut}/{$kodeTemplate}/INOVINDO/{$bulanRomawi}/{$tahunSingkat}";
    }

    private function deriveKodeTemplate(string $namaTemplate): string
    {
        $bersih   = preg_replace('/\(.*?\)/', '', $namaTemplate);
        $kataKata = preg_split('/\s+/', trim($bersih));
        $inisial  = array_map(fn($kata) => strtoupper(substr($kata, 0, 1)), $kataKata);

        return implode('', array_slice($inisial, 0, 4));
    }

    private function bulanRomawi(int $bulan): string
    {
        $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

        return $romawi[$bulan - 1];
    }
}
