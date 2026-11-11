@extends('layouts.app')

@section('page-title', 'Kelola Kriteria Retensi')

@section('page-css')
    @vite('resources/css/retensi-kriteria.css')
@endsection

@section('content')

    @if (in_array(auth()->user()->role?->nama_role ?? '', ['Super Admin', 'Admin']))
        <div class="retensi-tabs">
            <a href="{{ route('retensi-kriteria.index') }}" class="retensi-tabs__link is-active">
                Kelola Kriteria Retensi
            </a>
            <a href="{{ route('evaluasi-retensi.index') }}" class="retensi-tabs__link">
                Evaluasi Retensi Arsip
            </a>
        </div>
    @endif

    @php
        $n = $kriteria->count();
        $pasangan = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $pasangan[] = [$i, $j];
            }
        }
        $nilaiTerpilih = function (int $k, int $i, int $j) use ($ahp) {
            $lama = old("perbandingan.$k");
            if ($lama !== null) return $lama;
            if (!$ahp) return '1';
            $v = $ahp->matriks[$i][$j];
            for ($d = 1; $d <= 9; $d++) {
                if (abs($v - $d) < 1e-6) return (string) $d;
                if (abs($v - 1 / $d) < 1e-6) return $d === 1 ? '1' : "1/$d";
            }
            return '1';
        };
    @endphp

    <div class="rk-page">
        <h2 class="rk-page__title">Kelola Kriteria Retensi Arsip</h2>
        <p class="rk-page__subtitle">
            Bobot kriteria dihitung dengan <strong>AHP</strong> dari perbandingan berpasangan (skala Saaty 1–9),
            lalu dipakai oleh <strong>TOPSIS</strong> untuk merangking arsip. Bobot tidak dapat diisi manual.
        </p>

        @if (session('success'))
            <div class="rk-alert rk-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="rk-alert rk-alert--error">{{ $errors->first() }}</div>
        @endif

        <h3>Perbandingan Berpasangan</h3>
        <p>Pilih seberapa penting kriteria di kiri dibanding kriteria di kanan
            (9 = mutlak lebih penting, 1 = sama penting, 1/9 = kriteria kanan mutlak lebih penting).</p>

        <form method="POST" action="{{ route('retensi-kriteria.ahp') }}">
            @csrf
            <div class="rk-table-container">
                <table class="rk-table">
                    <thead>
                        <tr>
                            <th>Kriteria A</th>
                            <th>Nilai A terhadap B</th>
                            <th>Kriteria B</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasangan as $k => [$i, $j])
                            <tr>
                                <td>{{ $kriteria[$i]->kode }} – {{ $kriteria[$i]->nama_kriteria }}</td>
                                <td>
                                    <select name="perbandingan[{{ $k }}]" required>
                                        @foreach ($nilaiSkala as $nilai)
                                            <option value="{{ $nilai }}" {{ $nilaiTerpilih($k, $i, $j) === $nilai ? 'selected' : '' }}>
                                                {{ $nilai }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $kriteria[$j]->kode }} – {{ $kriteria[$j]->nama_kriteria }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit">Hitung Bobot &amp; Simpan</button>
        </form>

        @if ($ahp)
            <h3>Matriks Perbandingan Berpasangan</h3>
            <div class="rk-table-container">
                <table class="rk-table">
                    <thead>
                        <tr>
                            <th></th>
                            @foreach ($kriteria as $k) <th>{{ $k->kode }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteria as $i => $k)
                            <tr>
                                <td>{{ $k->kode }}</td>
                                @foreach ($kriteria as $j => $_)
                                    <td>{{ number_format($ahp->matriks[$i][$j], 4) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h3>Hasil Bobot &amp; Uji Konsistensi</h3>
            <div class="rk-table-container">
                <table class="rk-table">
                    <thead>
                        <tr>
                            <th>Kode</th><th>Nama Kriteria</th><th>Tipe</th><th>Bobot (AHP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kriteria as $k)
                            <tr>
                                <td>{{ $k->kode }}</td>
                                <td>{{ $k->nama_kriteria }}</td>
                                <td><span class="rk-tipe-badge rk-tipe-badge--{{ $k->tipe }}">{{ ucfirst($k->tipe) }}</span></td>
                                <td>{{ number_format($k->bobot, 4) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong>{{ number_format($kriteria->sum('bobot'), 4) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p>
                λmaks = {{ number_format($ahp->lambda_max, 4) }} ·
                CI = (λmaks − n) / (n − 1) = {{ number_format($ahp->ci, 4) }} ·
                RI (n = {{ $n }}) = {{ number_format($ahp->ri, 2) }} ·
                CR = CI / RI = <strong>{{ number_format($ahp->cr, 4) }}</strong>
                ({{ $ahp->cr <= 0.1 ? 'konsisten, CR ≤ 0,10' : 'TIDAK konsisten' }})
            </p>
        @endif

        <p><a href="{{ route('retensi-kriteria.perhitungan') }}">Lihat detail perhitungan TOPSIS</a></p>
    </div>
@endsection
