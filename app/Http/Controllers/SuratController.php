<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IsiSurat;
use App\Models\LogAktivitas;
use App\Models\PengaturanKopSurat;
use App\Models\Surat;
use App\Models\TemplateSurat;
use App\Models\User;
use App\Services\PlaceholderParser;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratController extends Controller
{
    public function create()
    {
        $templates = TemplateSurat::where('is_aktif', true)
            ->orderBy('nama_template')
            ->get();

        return view('surat.pilih-template', compact('templates'));
    }

    public function riwayat(Request $request)
    {
        $query = Surat::with(['template', 'kopSurat'])
            ->where('user_id', Auth::id())
            ->where('jenis', 'Keluar')
            ->latest('tanggal_surat');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                ->orWhereHas('template', function ($qt) use ($search) {
                    $qt->where('nama_template', 'like', "%{$search}%");
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

        $suratList = $query->paginate(10)->withQueryString();

        $templates = TemplateSurat::orderBy('nama_template')->get();

        return view('surat.riwayat', compact('suratList', 'templates'));
    }

    public function form(TemplateSurat $template)
    {
        $fields = PlaceholderParser::toFormFields($template->isi_template);
        $users = User::orderBy('name')->get();

        $kopSurats = PengaturanKopSurat::orderBy('versi', 'desc')->get();

        return view('surat.form', compact('template', 'fields', 'users', 'kopSurats'));
    }

    public function store(Request $request, TemplateSurat $template)
    {
        $validated = $request->validate([
            'kop_surat_id'   => ['required', 'exists:pengaturan_kop_surat,id'],
            'ttd_by_user_id' => ['required', 'exists:users,id'],
            'lampiran'       => ['nullable', 'string'],
            'fields'         => ['required', 'array'],
            'fields.*'       => ['required', 'string'],
        ]);

        $nomorSurat = $this->generateNomorSurat($template);

        $surat = Surat::create([
            'template_id'    => $template->id,
            'user_id'        => Auth::id(),
            'ttd_by_user_id' => $validated['ttd_by_user_id'],
            'kop_surat_id'   => $validated['kop_surat_id'],
            'nomor_surat'    => $nomorSurat,
            'lampiran'       => $request->input('lampiran', '-'),
            'jenis'          => 'Keluar',
            'tanggal_surat'  => now()->toDateString(),
        ]);

        foreach ($validated['fields'] as $namaField => $nilaiField) {
            IsiSurat::create([
                'surat_id'    => $surat->id,
                'nama_field'  => $namaField,
                'nilai_field' => $nilaiField,
                'created_at'  => now(),
            ]);
        }

        $this->generatePdfArsip($surat);

        LogAktivitas::catat(
            'Membuat',
            'Surat',
            "Membuat surat \"{$nomorSurat}\" dari template \"{$template->nama_template}\""
        );

        return redirect()->route('surat.show', $surat)
            ->with('success', "Surat berhasil dibuat dengan nomor {$nomorSurat} dan tersimpan ke Arsip.");
    }

    public function show(Surat $surat)
    {
        $surat->increment('jumlah_akses');

        return view('surat.preview', $this->buildCetakData($surat));
    }

    public function cetak(Surat $surat)
    {
        return view('surat.cetak', $this->buildCetakData($surat));
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

            $surat->file_arsip = $path;
            $surat->save();
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

        $this->generatePdfArsip($surat);

        LogAktivitas::catat('Mengubah', 'Surat', "Mengedit isi surat \"{$surat->nomor_surat}\"");

        return redirect()->route('surat.show', $surat)
            ->with('success', 'Isi surat berhasil diperbarui dan arsip PDF telah diperbarui.');
    }

    public function ttdDigital(Surat $surat)
    {
        $user = Auth::user();

        $isAdmin = $user && in_array(
            $user->role?->nama_role,
            ['Super Admin', 'Admin']
        );

        if (!$user || ($user->id != $surat->ttd_by_user_id && !$isAdmin)) {
            return redirect()->back()->with(
                'error',
                'Anda tidak memiliki hak untuk menandatangani surat ini!'
            );
        }

        $surat->status = 'Disetujui';
        $surat->save();

        $this->generatePdfArsip($surat);

        LogAktivitas::catat(
            'ACC',
            'Surat',
            "Menandatangani digital surat \"{$surat->nomor_surat}\""
        );

        return redirect()->back()->with(
            'success',
            'Surat berhasil disetujui dan file arsip PDF otomatis diperbarui!'
        );
    }

    public function updatePenandatangan(Request $request, Surat $surat)
    {
        $validated = $request->validate([
            'ttd_by_user_id' => ['required', 'exists:users,id'],
        ]);

        $surat->update([
            'ttd_by_user_id' => $validated['ttd_by_user_id']
        ]);

        LogAktivitas::catat('Mengubah', 'Surat', "Mengubah penandatangan surat \"{$surat->nomor_surat}\"");

        return redirect()->back()->with('success', 'Penandatangan surat berhasil diperbarui!');
    }

    public function destroy(Surat $surat)
    {
        if ($surat->file_arsip && Storage::disk('public')->exists($surat->file_arsip)) {
            Storage::disk('public')->delete($surat->file_arsip);
        }

        $surat->delete();

        return redirect()->back()->with('success', 'Surat berhasil dihapus!');
    }

    private function buildCetakData(Surat $surat): array
    {
        $surat->load(['template', 'kopSurat', 'isiSurat', 'user', 'penandatangan']);

        $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();
        $isiSuratRendered = PlaceholderParser::render($surat->template->isi_template, $values);

        $namaPenandatangan = $surat->penandatangan?->name
                        ?? $values['nama_penandatangan']
                        ?? $values['Nama Penandatangan']
                        ?? $values['penandatangan']
                        ?? $surat->user?->name;

        $jabatanPenandatangan = $values['jabatan_penandatangan']
                            ?? $values['Jabatan Penandatangan']
                            ?? $values['jabatan']
                            ?? $surat->penandatangan?->jabatan
                            ?? 'Direktur PT. Inovindo Digital Media';

        $isiUtama = $isiSuratRendered;
        $isiLampiran = null;

        if (stripos($isiSuratRendered, 'page-break-before: always') !== false) {
            $parts = preg_split(
                '/<div[^>]*style=["\'][^"\']*page-break-before:\s*always[^"\']*["\'][^>]*><\/div>/i',
                $isiSuratRendered,
                2
            );

            if (count($parts) === 2) {
                $isiUtama = $parts[0];
                $isiLampiran = $parts[1];
            }
        }

        $qrDataUri = null;
        if (strtolower($surat->status ?? '') === 'disetujui') {
            $qrDataUri = $this->buildQrDataUri(route('surat.show', $surat));
        }

        return compact(
            'surat',
            'isiSuratRendered',
            'isiUtama',
            'isiLampiran',
            'namaPenandatangan',
            'jabatanPenandatangan',
            'qrDataUri'
        );
    }

    private function buildQrDataUri(string $data): ?string
    {
        try {
            $svg = QrCode::size(100)->margin(1)->generate($data);
            if (empty($svg)) {
                return null;
            }
            
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildLogoDataUri(?string $logoPath): ?string
    {
        if (!$logoPath || !Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        try {
            $fileContent = Storage::disk('public')->get($logoPath);
            if (empty($fileContent)) {
                return null;
            }

            $mimeType = Storage::mimeType('public/' . $logoPath) ?? 'image/png';
            return 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function generatePdfArsip(Surat $surat): void
    {
        $data = $this->buildCetakData($surat);


        $logoPath = $surat->kopSurat->logo_path ?? $surat->kopSurat->logo ?? null;
        $data['logoDataUri'] = $logoPath;
        
        //  dd(storage_path('app/public/' . $logoPath));

        // dd($data['logoDataUri']);
        

        $pdf = Pdf::loadView('surat.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]) ;

            // dd($pdf);

        $namaFile = 'arsip_surat/' . str_replace('/', '_', $surat->nomor_surat) . '.pdf';

        Storage::disk('public')->put($namaFile, $pdf->output());

        $surat->forceFill([
            'file_arsip' => $namaFile
        ])->save();
    }

    private function generateNomorSurat(TemplateSurat $template): string
    {
        $tahunPenuh = now()->year;
        $bulanIni   = now()->month;

        $runningNumber = Surat::where('template_id', $template->id)
            ->whereYear('created_at', $tahunPenuh)
            ->whereMonth('created_at', $bulanIni)
            ->count() + 1;

        $nomorUrut    = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);
        $kodeTemplate = $this->deriveKodeTemplate($template->nama_template);
        $bulanRomawi  = $this->bulanRomawi($bulanIni);

        return "{$nomorUrut}/{$kodeTemplate}/INOVINDO/{$bulanRomawi}/{$tahunPenuh}";
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
