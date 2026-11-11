<?php

namespace App\Http\Controllers;

use App\Models\RetensiAhp;
use App\Models\RetensiKriteria;
use App\Models\User;
use App\Services\AhpCalculator;
use App\Services\RetensiScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RetensiKriteriaController extends Controller
{
    public const NILAI_SKALA = [
        '9', '8', '7', '6', '5', '4', '3', '2', '1',
        '1/2', '1/3', '1/4', '1/5', '1/6', '1/7', '1/8', '1/9',
    ];

    public function index(Request $request, RetensiScoringService $scoring)
    {
        $unitId = $this->unitAktif($request);
        $scoring->siapkanUnit($unitId);

        return view('retensi-kriteria.index', [
            'kriteria'   => RetensiKriteria::where('unit_kerja_id', $unitId)->orderBy('kode')->get(),
            'ahp'        => RetensiAhp::where('unit_kerja_id', $unitId)->first(),
            'nilaiSkala' => self::NILAI_SKALA,
            'unitId'     => $unitId,
        ]);
    }

    public function simpanAhp(Request $request, AhpCalculator $ahp, RetensiScoringService $scoring)
    {
        $unitId = $this->unitAktif($request);
        $scoring->siapkanUnit($unitId);

        $kriteria = RetensiKriteria::where('unit_kerja_id', $unitId)->orderBy('kode')->get();
        $n = $kriteria->count();
        $jumlahPasangan = $n * ($n - 1) / 2;

        $validated = $request->validate([
            'perbandingan'   => ['required', 'array', "size:{$jumlahPasangan}"],
            'perbandingan.*' => ['required', 'in:' . implode(',', self::NILAI_SKALA)],
        ]);

        $segitiga = array_map(fn ($v) => $this->keAngka($v), array_values($validated['perbandingan']));

        try {
            $matriks = $ahp->bangunMatriks($n, $segitiga);
            $hasil = $ahp->hitung($matriks);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['ahp' => $e->getMessage()])->withInput();
        }

        if (!$hasil['konsisten']) {
            return back()
                ->withErrors(['ahp' => sprintf(
                    'Penilaian tidak konsisten: CR = %.4f (batas %.2f). Perbaiki perbandingan lalu simpan ulang. Bobot tidak diubah.',
                    $hasil['cr'],
                    AhpCalculator::BATAS_CR
                )])
                ->withInput();
        }

        DB::transaction(fn () => $scoring->simpanHasilAhp($unitId, $kriteria, $matriks, $hasil, Auth::id()));

        $jumlah = $scoring->evaluasiMassal($unitId);

        return back()->with(
            'success',
            sprintf(
                'Bobot AHP disimpan (CR = %.4f, konsisten). %d arsip unit ini dihitung ulang dengan TOPSIS dan menunggu validasi ulang.',
                $hasil['cr'],
                $jumlah
            )
        );
    }

    public function perhitungan(Request $request, RetensiScoringService $scoring)
    {
        $unitId = $this->unitAktif($request);

        return view('retensi-kriteria.perhitungan', [
            'hitung'     => $scoring->detail($unitId),
            'ahp'        => RetensiAhp::where('unit_kerja_id', $unitId)->first(),
            'unitId'     => $unitId,
        ]);
    }

    private function unitAktif(Request $request): int
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user->unit_kerja_id, 403, 'Akun Anda belum terhubung ke unit kerja.');

        return (int) $user->unit_kerja_id;
    }

    private function keAngka(string $nilai): float
    {
        if (str_contains($nilai, '/')) {
            [$a, $b] = explode('/', $nilai, 2);

            return (float) $a / (float) $b;
        }

        return (float) $nilai;
    }
}
