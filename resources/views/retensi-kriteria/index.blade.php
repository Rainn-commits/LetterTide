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

        <section class="rk-section">
            <h3 class="rk-section__title">Perbandingan Berpasangan</h3>
            <p class="rk-text">
                Pilih seberapa penting kriteria di kiri dibanding kriteria di kanan
                (9 = mutlak lebih penting, 1 = sama penting, 1/9 = kriteria kanan mutlak lebih penting).
            </p>

            <form method="POST" action="{{ route('retensi-kriteria.ahp') }}">
                @csrf
                <div class="rk-table-container">
                    <table class="rk-table rk-table--kiri">
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
                                        <select name="perbandingan[{{ $k }}]" class="rk-select" required>
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

                <div class="rk-form-aksi">
                    <button type="submit" class="rk-btn">Hitung Bobot &amp; Simpan</button>
                </div>
            </form>
        </section>

        @if ($ahp)
            <section class="rk-section">
                <h3 class="rk-section__title">Matriks Perbandingan Berpasangan</h3>
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
                                    <td><strong>{{ $k->kode }}</strong></td>
                                    @foreach ($kriteria as $j => $_)
                                        <td>{{ number_format($ahp->matriks[$i][$j], 4) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rk-section">
                <h3 class="rk-section__title">Hasil Bobot &amp; Uji Konsistensi</h3>
                <div class="rk-table-container">
                    <table class="rk-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Kriteria</th>
                                <th>Tipe</th>
                                <th>Bobot (AHP)</th>
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
                            <tr class="rk-baris-total">
                                <td colspan="3">Total</td>
                                <td>{{ number_format($kriteria->sum('bobot'), 4) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p class="rk-text rk-konsistensi">
                    λmaks = {{ number_format($ahp->lambda_max, 4) }} ·
                    CI = (λmaks − n) / (n − 1) = {{ number_format($ahp->ci, 4) }} ·
                    RI (n = {{ $n }}) = {{ number_format($ahp->ri, 2) }} ·
                    CR = CI / RI = <strong>{{ number_format($ahp->cr, 4) }}</strong>
                    @if ($ahp->cr <= 0.1)
                        <span class="rk-status rk-status--ok">Konsisten (CR ≤ 0,10)</span>
                    @else
                        <span class="rk-status rk-status--gagal">Tidak konsisten</span>
                    @endif
                </p>
            </section>
        @endif

        <div class="rk-aksi-halaman">
            <a href="{{ route('retensi-kriteria.perhitungan') }}" class="rk-btn">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                    <path d="M8.5 7H15.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M8.5 11.5H8.51M12 11.5H12.01M15.5 11.5H15.51M8.5 15H8.51M12 15H12.01M15.5 15H15.51M8.5 18H8.51M12 18H15.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                </svg>
                Lihat Detail Perhitungan TOPSIS
            </a>
        </div>
    </div>
@endsection
