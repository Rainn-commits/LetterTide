<?php

namespace App\Http\Controllers;

use App\Models\PengaturanKopSurat;
use App\Models\LogAktivitas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KopSuratController extends Controller
{
    private const LOGO_LEBAR  = 400;
    private const LOGO_TINGGI = 150;

    public function index()
    {
        $riwayat = PengaturanKopSurat::orderByDesc('versi')
            ->get();

        return view('kop-surat.index', [
            'riwayat' => $riwayat,
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
            'nama_pt'   => ['required', 'string', 'max:100'],
            'alamat_pt' => ['required', 'string', 'max:255'],
            'telepon'   => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:100'],
            'website'   => ['nullable', 'string', 'max:100'],
        ]);

        // Setiap kop surat BARU selalu dimulai dari versi 1.
        $versiBaru = 1;

        if ($request->hasFile('logo')) {
            $logoPath = $this->prosesLogo($request->file('logo'));
        } else {
            $logoPath = null;
        }

        PengaturanKopSurat::create([
            'logo'       => $logoPath,
            'nama_pt'    => $validated['nama_pt'],
            'alamat_pt'  => $validated['alamat_pt'],
            'telepon'    => $validated['telepon'],
            'email'      => $validated['email'],
            'website'    => $validated['website'] ?? null,
            'versi'      => $versiBaru,
            'created_at' => now(),
        ]);

        LogAktivitas::catat(
            'Membuat',
            'Kop Surat',
            "Membuat kop surat \"{$validated['nama_pt']}\" versi {$versiBaru}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat berhasil dibuat sebagai versi {$versiBaru}."
            );
    }

    public function edit($id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        return view('kop-surat.edit', compact('kopSurat'));
    }

    public function update(Request $request, $id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        $validated = $request->validate([
            'logo'      => ['nullable', 'image', 'max:10240'],
            'nama_pt'   => ['required', 'string', 'max:100'],
            'alamat_pt' => ['required', 'string', 'max:255'],
            'telepon'   => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email', 'max:100'],
            'website'   => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $this->prosesLogo($request->file('logo'));
        } else {
            $logoPath = $kopSurat->logo;
        }

        // Edit selalu membuat versi baru.
        $versiBaru = $kopSurat->versi + 1;

        PengaturanKopSurat::create([
            'logo'       => $logoPath,
            'nama_pt'    => $validated['nama_pt'],
            'alamat_pt'  => $validated['alamat_pt'],
            'telepon'    => $validated['telepon'],
            'email'      => $validated['email'],
            'website'    => $validated['website'] ?? null,
            'versi'      => $versiBaru,
            'created_at' => now(),
        ]);

        LogAktivitas::catat(
            'Mengubah',
            'Kop Surat',
            "Memperbarui kop surat \"{$validated['nama_pt']}\" ke versi {$versiBaru}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat berhasil disimpan sebagai versi {$versiBaru}."
            );
    }

    public function destroy($id)
    {
        $kopSurat = PengaturanKopSurat::findOrFail($id);

        $versi = $kopSurat->versi;
        $nama  = $kopSurat->nama_pt;

        if ($kopSurat->logo && Storage::disk('public')->exists($kopSurat->logo)) {
            Storage::disk('public')->delete($kopSurat->logo);
        }

        // Hard delete.
        $kopSurat->delete();

        LogAktivitas::catat(
            'Menghapus',
            'Kop Surat',
            "Menghapus kop surat \"{$nama}\" versi {$versi}"
        );

        return redirect()->route('kop-surat.index')
            ->with(
                'success',
                "Kop surat versi {$versi} berhasil dihapus."
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
