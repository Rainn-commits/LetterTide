<?php

namespace App\Services;

use App\Models\EvaluasiRetensi;
use App\Models\RetensiAhp;
use App\Models\RetensiKriteria;
use App\Models\Surat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetensiScoringService
{
    public const KRITERIA_BAWAAN = [
        ['kode' => 'K1', 'nama_kriteria' => 'Nilai Guna Dokumen',         'tipe' => 'benefit'],
        ['kode' => 'K2', 'nama_kriteria' => 'Sifat / Kerahasiaan Surat',  'tipe' => 'benefit'],
        ['kode' => 'K3', 'nama_kriteria' => 'Masa Simpan / Umur Surat',   'tipe' => 'cost'],
        ['kode' => 'K4', 'nama_kriteria' => 'Frekuensi Akses / Download', 'tipe' => 'benefit'],
    ];

    public const PERBANDINGAN_AWAL = [2, 3, 5, 2, 3, 2];

    private const SKALA_NILAI_GUNA = [
        'Hukum'         => 4,
        'Keuangan'      => 3,
        'Operasional'   => 2,
        'Pemberitahuan' => 1,
    ];

    private const SKALA_KERAHASIAAN = [
        'Sangat Rahasia' => 3,
        'Rahasia'        => 2,
        'Biasa/Terbuka'  => 1,
    ];

    public const AMBANG_PERMANEN     = 0.70;
    public const AMBANG_SIMPAN_LOKAL = 0.40;

    private const URUTAN_KRITERIA = ['K1', 'K2', 'K3', 'K4'];

    public function __construct(
        private TopsisCalculator $topsis = new TopsisCalculator(),
        private AhpCalculator $ahp = new AhpCalculator(),
    ) {
    }

    public function siapkanUnit(int $unitId): void
    {
        DB::transaction(function () use ($unitId) {
            $kriteria = RetensiKriteria::where('unit_kerja_id', $unitId)->orderBy('kode')->get();

            if ($kriteria->isEmpty()) {
                foreach (self::KRITERIA_BAWAAN as $k) {
                    RetensiKriteria::create($k + ['bobot' => 0.25, 'unit_kerja_id' => $unitId]);
                }
                $kriteria = RetensiKriteria::where('unit_kerja_id', $unitId)->orderBy('kode')->get();
            }

            $sudahAda = RetensiAhp::where('unit_kerja_id', $unitId)->exists();

            if ($sudahAda || $kriteria->count() !== count(self::KRITERIA_BAWAAN)) {
                return;
            }

            $matriks = $this->ahp->bangunMatriks($kriteria->count(), self::PERBANDINGAN_AWAL);
            $hasil = $this->ahp->hitung($matriks);

            $this->simpanHasilAhp($unitId, $kriteria, $matriks, $hasil, null);
        });
    }

    public function simpanHasilAhp(int $unitId, $kriteria, array $matriks, array $hasil, ?int $userId): void
    {
        foreach ($kriteria->values() as $i => $k) {
            $k->update(['bobot' => $hasil['bobot'][$i]]);
        }

        RetensiAhp::updateOrCreate(['unit_kerja_id' => $unitId], [
            'matriks'     => $matriks,
            'lambda_max'  => $hasil['lambda_max'],
            'ci'          => $hasil['ci'],
            'ri'          => $hasil['ri'],
            'cr'          => $hasil['cr'],
            'diubah_oleh' => $userId,
        ]);
    }

    public function evaluasiMassal(?int $unitId = null): int
    {
        $daftarUnit = $unitId
            ? [$unitId]
            : Surat::query()->whereNotNull('unit_kerja_id')->distinct()->pluck('unit_kerja_id')->all();

        $total = 0;
        foreach ($daftarUnit as $id) {
            $total += $this->evaluasiUnit((int) $id);
        }

        return $total;
    }

    public function detail(int $unitId): ?array
    {
        return $this->jalankan($unitId);
    }

    private function evaluasiUnit(int $unitId): int
    {
        $hitung = $this->jalankan($unitId);

        if ($hitung === null) {
            return 0;
        }

        foreach ($hitung['items'] as $item) {
            $id = $item->id;
            $ci = $hitung['topsis']['ci'][$id];

            $item->update([
                'skor_total'         => round($ci, 6),
                'd_plus'             => $hitung['topsis']['d_plus'][$id],
                'd_minus'            => $hitung['topsis']['d_minus'][$id],
                'status_rekomendasi' => self::klasifikasikan($ci),
                'status_persetujuan' => 'Menunggu Validasi',
                'dievaluasi_pada'    => now(),
                'dievaluasi_oleh'    => Auth::id(),
            ]);
        }

        return $hitung['items']->count();
    }

    private function jalankan(int $unitId): ?array
    {
        $this->siapkanUnit($unitId);

        $kriteria = RetensiKriteria::where('unit_kerja_id', $unitId)->get()->keyBy('kode');

        $items = EvaluasiRetensi::with('surat')
            ->whereHas('surat', fn ($q) => $q->where('unit_kerja_id', $unitId))
            ->whereNotNull('nilai_guna')
            ->whereNotNull('kerahasiaan')
            ->get();

        if ($items->isEmpty()) {
            return null;
        }

        $bobot = [];
        $tipe = [];
        foreach (self::URUTAN_KRITERIA as $kode) {
            $bobot[] = (float) ($kriteria[$kode]->bobot ?? 0);
            $tipe[] = $kriteria[$kode]->tipe ?? 'benefit';
        }

        $jumlah = array_sum($bobot);
        $bobot = $jumlah > 0
            ? array_map(fn ($b) => $b / $jumlah, $bobot)
            : array_fill(0, count($bobot), 1 / count($bobot));

        $matriks = [];
        foreach ($items as $item) {
            $umurHari = Carbon::parse($item->surat->tanggal_surat)->diffInDays(now());

            $matriks[$item->id] = [
                self::SKALA_NILAI_GUNA[$item->nilai_guna] ?? 1,
                self::SKALA_KERAHASIAAN[$item->kerahasiaan] ?? 1,
                $umurHari,
                $item->surat->jumlah_akses ?? 0,
            ];
        }

        return [
            'items'    => $items,
            'kriteria' => $kriteria,
            'bobot'    => $bobot,
            'tipe'     => $tipe,
            'matriks'  => $matriks,
            'topsis'   => $this->topsis->hitung($matriks, $bobot, $tipe),
        ];
    }

    public static function klasifikasikan(float $ci): string
    {
        if ($ci >= self::AMBANG_PERMANEN) {
            return 'Permanen';
        }

        if ($ci >= self::AMBANG_SIMPAN_LOKAL) {
            return 'Simpan Lokal';
        }

        return 'Musnah';
    }
}
