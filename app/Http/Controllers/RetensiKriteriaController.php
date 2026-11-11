<?php

namespace App\Http\Controllers;

use App\Models\RetensiAhp;
use App\Models\RetensiKriteria;
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

    public function index()
    {
        $kriteria = RetensiKriteria::orderBy('kode')->get();
        $ahp = RetensiAhp::find(1);

        return view('retensi-kriteria.index', [
            'kriteria'   => $kriteria,
            'ahp'        => $ahp,
            'nilaiSkala' => self::NILAI_SKALA,
        ]);
    }

    public function simpanAhp(Request $request, AhpCalculator $ahp, RetensiScoringService $scoring)
    {
        $kriteria = RetensiKriteria::orderBy('kode')->get();
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

        DB::transaction(function () use ($kriteria, $hasil, $matriks) {
            foreach ($kriteria as $i => $k) {
                $k->update(['bobot' => $hasil['bobot'][$i]]);
            }

            RetensiAhp::updateOrCreate(['id' => 1], [
                'matriks'     => $matriks,
                'lambda_max'  => $hasil['lambda_max'],
                'ci'          => $hasil['ci'],
                'ri'          => $hasil['ri'],
                'cr'          => $hasil['cr'],
                'diubah_oleh' => Auth::id(),
            ]);
        });

        $jumlah = $scoring->evaluasiMassal();

        return back()->with(
            'success',
            sprintf(
                'Bobot AHP disimpan (CR = %.4f, konsisten). %d arsip dihitung ulang dengan TOPSIS dan menunggu validasi ulang.',
                $hasil['cr'],
                $jumlah
            )
        );
    }

    public function perhitungan(RetensiScoringService $scoring)
    {
        return view('retensi-kriteria.perhitungan', [
            'hitung' => $scoring->detail(),
            'ahp'    => RetensiAhp::find(1),
        ]);
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

