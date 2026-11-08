<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKopSurat;
use App\Models\LogAktivitas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KopSuratController extends Controller
{
    private const LOGO_LEBAR  = 400;
    private const LOGO_TINGGI = 150;

    public function index()
    {
        $riwayat = PengaturanKopSurat::with('unitKerja')->orderBy('nama_kop')
            ->orderByDesc('versi')
            ->get();

        return view('kop-surat.index', [
            'daftarKop' => $riwayat,
        ]);
    }

    public function create()
    {
        return view('kop-surat.create');
    }

    public function preview(PengaturanKopSurat $pengaturanKopSurat)
    {
        return view('kop-surat.preview', [
            'kopSurat' => $pengaturanKopSurat,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo'      => ['nullable', 'image', 'max:10240'],
            'nama_kop'  => ['required', 'string', 'max:100'],
            'nama_pt'   => ['required', 'string', 'max:100'],
            'alamat_pt' => ['required', 'string', 'max:255'],
            'telepon'   => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:100'],
            'website'   => ['nullable', 'string', 'max:100'],
            'unit_kerja_id' => ['required', 'exists:unit_kerja,id'],
        ], [
            'unit_kerja_id.required' => 'Unit kerja pemilik kop wajib dipilih.',
        ]);

        $versiBaru = 1;

        if ($request->hasFile('logo')) {
            $logoPath = $this->prosesLogo($request->file('logo'));
        } else {
            $logoPath = null;
        }

        PengaturanKopSurat::create([
            'logo'       => $logoPath,
            'nama_kop'   => $validated['nama_kop'],
            'nama_pt'    => $validated['nama_pt'],
            'alamat_pt'  => $validated['alamat_pt'],
            'telepon'    => $validated['telepon'],
            'email'      => $validated['email'],
            'website'    => $validated['website'] ?? null,
            'versi'      => $versiBaru,
            'is_aktif'   => true,
            'created_at' => now(),
            'unit_kerja_id' => $validated['unit_kerja_id'],
        ]);

        LogAktivitas::catat(
            'Membuat',
            'Kop Surat',
            "Membuat kop surat \"{$validated['nama_kop']}\" untuk \"{$validated['nama_pt']}\" versi {$versiBaru}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat \"{$validated['nama_kop']}\" berhasil dibuat sebagai versi {$versiBaru}."
            );
    }

    public function edit($id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        if ($tolak = $this->tolakJikaTidakAktif($kopSurat)) {
            return $tolak;
        }

        return view('kop-surat.edit', compact('kopSurat'));
    }

    public function update(Request $request, $id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        if ($tolak = $this->tolakJikaTidakAktif($kopSurat)) {
            return $tolak;
        }

        $validated = $request->validate([
            'logo'      => ['nullable', 'image', 'max:10240'],
            'nama_kop'  => ['required', 'string', 'max:100'],
            'nama_pt'   => ['required', 'string', 'max:100'],
            'alamat_pt' => ['required', 'string', 'max:255'],
            'telepon'   => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:100'],
            'website'   => ['nullable', 'string', 'max:100'],
            'unit_kerja_id' => [auth()->user()->isGlobal() ? 'required' : 'nullable', 'exists:unit_kerja,id'],
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $this->prosesLogo($request->file('logo'));
        } else {
            $logoPath = $kopSurat->logo;
        }

        $versiBaru = $kopSurat->versi + 1;

        $unitId = auth()->user()->isGlobal()
            ? $validated['unit_kerja_id']
            : $kopSurat->unit_kerja_id;

        DB::transaction(function () use ($kopSurat, $validated, $logoPath, $versiBaru, $unitId) {
            if ((int) $unitId !== (int) $kopSurat->unit_kerja_id) {
                PengaturanKopSurat::where('nama_kop', $kopSurat->nama_kop)
                    ->where('unit_kerja_id', $kopSurat->unit_kerja_id)
                    ->update(['unit_kerja_id' => $unitId]);
            }

            $kopSurat->update(['is_aktif' => false]);

            PengaturanKopSurat::create([
                'logo'       => $logoPath,
                'nama_kop'   => $validated['nama_kop'],
                'nama_pt'    => $validated['nama_pt'],
                'alamat_pt'  => $validated['alamat_pt'],
                'telepon'    => $validated['telepon'],
                'email'      => $validated['email'],
                'website'    => $validated['website'] ?? null,
                'versi'      => $versiBaru,
                'is_aktif'   => true,
                'created_at' => now(),
                'unit_kerja_id' => $unitId,
            ]);
        });

        LogAktivitas::catat(
            'Mengubah',
            'Kop Surat',
            "Memperbarui kop surat \"{$validated['nama_kop']}\" ke versi {$versiBaru}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat \"{$validated['nama_kop']}\" berhasil disimpan sebagai versi {$versiBaru}."
            );
    }

    public function destroy($id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        $versi = $kopSurat->versi;
        $nama  = $kopSurat->nama_kop;

        if ($kopSurat->surat()->exists()) {
            return redirect()->route('kop-surat.index')
                ->with('error', "Kop surat \"{$nama}\" versi {$versi} tidak bisa dihapus karena sudah dipakai oleh surat.");
        }

        $logo = $kopSurat->logo;
        $sedangAktif = $kopSurat->is_aktif;

        $unitId = $kopSurat->unit_kerja_id;

        DB::transaction(function () use ($kopSurat, $nama, $sedangAktif, $unitId) {
            $kopSurat->delete();

            if ($sedangAktif) {
                PengaturanKopSurat::where('nama_kop', $nama)
                    ->where('unit_kerja_id', $unitId)
                    ->orderByDesc('versi')
                    ->first()
                    ?->update(['is_aktif' => true]);
            }
        });

        if ($logo
            && ! PengaturanKopSurat::withoutGlobalScopes()->where('logo', $logo)->exists()
            && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        LogAktivitas::catat(
            'Menghapus',
            'Kop Surat',
            "Menghapus kop surat \"{$nama}\" versi {$versi}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat \"{$nama}\" versi {$versi} berhasil dihapus."
            );
    }

    private function tolakJikaTidakAktif(PengaturanKopSurat $kopSurat)
    {
        if ($kopSurat->is_aktif) {
            return null;
        }

        return redirect()->route('kop-surat.index')
            ->with(
                'error',
                "Kop surat \"{$kopSurat->nama_kop}\" versi {$kopSurat->versi} sudah tidak aktif dan tidak dapat diedit. Edit versi yang berstatus Aktif."
            );
    }

    private function prosesLogo($file): string
    {
        $ekstensi = strtolower($file->getClientOriginalExtension());

        $sumber = match ($ekstensi) {
            'png' => @imagecreatefrompng($file->getRealPath()),
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'gif' => @imagecreatefromgif($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (!$sumber) {
            return $file->store('kop-surat', 'public');
        }

        $lebarAsli  = imagesx($sumber);
        $tinggiAsli = imagesy($sumber);

        $rasio = min(
            self::LOGO_LEBAR / $lebarAsli,
            self::LOGO_TINGGI / $tinggiAsli
        );

        $lebarBaru  = (int) round($lebarAsli * $rasio);
        $tinggiBaru = (int) round($tinggiAsli * $rasio);

        $kanvas = imagecreatetruecolor(
            self::LOGO_LEBAR,
            self::LOGO_TINGGI
        );

        $putih = imagecolorallocate($kanvas, 255, 255, 255);
        imagefill($kanvas, 0, 0, $putih);

        $offsetX = (int) round(
            (self::LOGO_LEBAR - $lebarBaru) / 2
        );

        $offsetY = (int) round(
            (self::LOGO_TINGGI - $tinggiBaru) / 2
        );

        imagecopyresampled(
            $kanvas,
            $sumber,
            $offsetX,
            $offsetY,
            0,
            0,
            $lebarBaru,
            $tinggiBaru,
            $lebarAsli,
            $tinggiAsli
        );

        $namaFile = 'kop-surat/logo_' . uniqid() . '.png';

        $pathFisik = storage_path(
            'app/public/' . $namaFile
        );

        if (!is_dir(dirname($pathFisik))) {
            mkdir(dirname($pathFisik), 0755, true);
        }

        imagepng($kanvas, $pathFisik);

        imagedestroy($sumber);
        imagedestroy($kanvas);

        return $namaFile;
    }
}
