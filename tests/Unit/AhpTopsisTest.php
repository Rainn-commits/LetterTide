<?php

namespace Tests\Unit;

use App\Services\AhpCalculator;
use App\Services\TopsisCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AhpTopsisTest extends TestCase
{
    public function test_ahp_contoh_klasik_saaty_3x3(): void
    {
        $hasil = (new AhpCalculator())->hitung([
            [1, 3, 5],
            [1 / 3, 1, 3],
            [1 / 5, 1 / 3, 1],
        ]);

        $this->assertEqualsWithDelta(0.6370, $hasil['bobot'][0], 0.0001);
        $this->assertEqualsWithDelta(0.2583, $hasil['bobot'][1], 0.0001);
        $this->assertEqualsWithDelta(0.1047, $hasil['bobot'][2], 0.0001);
        $this->assertEqualsWithDelta(3.0385, $hasil['lambda_max'], 0.0001);
        $this->assertEqualsWithDelta(0.0193, $hasil['ci'], 0.0001);
        $this->assertEqualsWithDelta(0.0332, $hasil['cr'], 0.0001);
        $this->assertTrue($hasil['konsisten']);
        $this->assertEqualsWithDelta(1.0, array_sum($hasil['bobot']), 1e-12);
    }

    public function test_ahp_kriteria_retensi_4x4(): void
    {
        $ahp = new AhpCalculator();
        $hasil = $ahp->hitung($ahp->bangunMatriks(4, [2, 3, 5, 2, 3, 2]));

        $this->assertEqualsWithDelta(0.4829, $hasil['bobot'][0], 0.0001);
        $this->assertEqualsWithDelta(0.2720, $hasil['bobot'][1], 0.0001);
        $this->assertEqualsWithDelta(0.1570, $hasil['bobot'][2], 0.0001);
        $this->assertEqualsWithDelta(0.0882, $hasil['bobot'][3], 0.0001);
        $this->assertEqualsWithDelta(4.0145, $hasil['lambda_max'], 0.0001);
        $this->assertEqualsWithDelta(0.0054, $hasil['cr'], 0.0001);
        $this->assertTrue($hasil['konsisten']);
    }

    public function test_ahp_menolak_penilaian_tidak_konsisten(): void
    {
        $ahp = new AhpCalculator();
        $hasil = $ahp->hitung($ahp->bangunMatriks(3, [9, 1 / 9, 9]));

        $this->assertGreaterThan(0.10, $hasil['cr']);
        $this->assertFalse($hasil['konsisten']);
    }

    public function test_ahp_menolak_matriks_tidak_reciprocal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new AhpCalculator())->hitung([[1, 3], [3, 1]]);
    }

    public function test_topsis_empat_alternatif_empat_kriteria(): void
    {
        $matriks = [
            'a' => [4, 3, 100, 10],
            'b' => [2, 1, 20, 0],
            'c' => [3, 2, 400, 5],
            'd' => [1, 1, 50, 30],
        ];
        $bobot = [0.4829, 0.2720, 0.1570, 0.0882];
        $bobot = array_map(fn ($b) => $b / array_sum($bobot), $bobot);

        $hasil = (new TopsisCalculator())->hitung(
            $matriks,
            $bobot,
            ['benefit', 'benefit', 'cost', 'benefit']
        );

        $this->assertEqualsWithDelta(0.8365, $hasil['ci']['a'], 0.0005);
        $this->assertEqualsWithDelta(0.4122, $hasil['ci']['b'], 0.0005);
        $this->assertEqualsWithDelta(0.4939, $hasil['ci']['c'], 0.0005);
        $this->assertEqualsWithDelta(0.3421, $hasil['ci']['d'], 0.0005);

        foreach ($hasil['ci'] as $ci) {
            $this->assertGreaterThanOrEqual(0.0, $ci);
            $this->assertLessThanOrEqual(1.0, $ci);
        }
    }

    public function test_topsis_cost_membalik_arah_ideal(): void
    {
        $hasil = (new TopsisCalculator())->hitung(
            ['kecil' => [10], 'besar' => [100]],
            [1.0],
            ['cost']
        );

        $this->assertEqualsWithDelta(1.0, $hasil['ci']['kecil'], 1e-12);
        $this->assertEqualsWithDelta(0.0, $hasil['ci']['besar'], 1e-12);
    }

    public function test_topsis_satu_alternatif_tidak_terdefinisi_dipakai_0_5(): void
    {
        $hasil = (new TopsisCalculator())->hitung(['x' => [3, 2]], [0.5, 0.5], ['benefit', 'benefit']);

        $this->assertEqualsWithDelta(0.5, $hasil['ci']['x'], 1e-12);
    }

    public function test_topsis_kolom_semua_nol_tidak_error(): void
    {
        $hasil = (new TopsisCalculator())->hitung(
            ['a' => [1, 0], 'b' => [2, 0]],
            [0.5, 0.5],
            ['benefit', 'benefit']
        );

        $this->assertEqualsWithDelta(0.0, $hasil['normalisasi']['a'][1], 1e-12);
        $this->assertEqualsWithDelta(1.0, $hasil['ci']['b'], 1e-12);
    }
}
