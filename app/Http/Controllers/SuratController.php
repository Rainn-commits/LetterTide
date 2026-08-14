<?php

namespace App\Http\Controllers;

use App\Models\IsiSurat;
use App\Models\PengaturanKopSurat;
use App\Models\Surat;
use App\Models\TemplateSurat;
use App\Services\PlaceholderParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('surat.form', compact('template', 'fields'));
    }

    public function store(Request $request, TemplateSurat $template)
    {
        $validated = $request->validate([
            'fields'   => ['required', 'array'],
            'fields.*' => ['required', 'string'],
        ]);

        $kopSuratAktif = PengaturanKopSurat::where('is_aktif', true)->first();

        if (!$kopSuratAktif) {
            return back()->withErrors([
                'kop_surat' => 'Kop surat belum diatur. Hubungi Admin sebelum membuat surat.',
            ]);
        }

        $nomorSurat = $this->generateNomorSurat($template);

        $surat = Surat::create([
            'template_id'   => $template->id,
            'user_id'       => Auth::id(),
            'kop_surat_id'  => $kopSuratAktif->id,
            'nomor_surat'   => $nomorSurat,
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

        return redirect()->route('surat.create')
            ->with('success', "Surat berhasil dibuat dengan nomor {$nomorSurat}.");
    }

    private function generateNomorSurat(TemplateSurat $template): string
    {
        $tahunPenuh    = now()->year;
        $tahunSingkat  = now()->format('y');
        $runningNumber = Surat::whereYear('created_at', $tahunPenuh)->count() + 1;
        $nomorUrut     = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);
        $kodeTemplate  = $this->deriveKodeTemplate($template->nama_template);
        $bulanRomawi   = $this->bulanRomawi(now()->month);

        return "{$nomorUrut}/{$kodeTemplate}/{$bulanRomawi}/{$tahunSingkat}";
    }

    private function deriveKodeTemplate(string $namaTemplate): string
    {
        $bersih   = preg_replace('/\(.*?\)/', '', $namaTemplate); // hapus teks dalam kurung
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