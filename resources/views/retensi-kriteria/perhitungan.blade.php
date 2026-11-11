@extends('layouts.app')

@section('page-title', 'Detail Perhitungan Retensi')

@section('page-css')
    @vite('resources/css/retensi-kriteria.css')
@endsection

@section('content')
    <div class="rk-page">
        <h2 class="rk-page__title">Detail Perhitungan Hybrid AHP + TOPSIS</h2>
        <p class="rk-page__subtitle">Setiap langkah dihitung dari data arsip yang sudah dinilai saat ini.</p>

        @if (!$hitung)
            <div class="rk-alert rk-alert--warning">Belum ada arsip yang dinilai (nilai guna dan kerahasiaan).</div>
        @else
            @php
                $kode = ['K1', 'K2', 'K3', 'K4'];
                $t = $hitung['topsis'];
                $f = fn ($x, $d = 4) => number_format($x, $d);
                $nomor = $hitung['items']->pluck('surat.nomor_surat', 'id');
            @endphp

            <h3>1. Bobot AHP &amp; Tipe Kriteria</h3>
            <div class="rk-table-container"><table class="rk-table">
                <thead><tr><th></th>@foreach ($kode as $k)<th>{{ $k }}</th>@endforeach</tr></thead>
                <tbody>
                    <tr><td>Bobot (w)</td>@foreach ($hitung['bobot'] as $b)<td>{{ $f($b) }}</td>@endforeach</tr>
                    <tr><td>Tipe</td>@foreach ($hitung['tipe'] as $tp)<td>{{ $tp }}</td>@endforeach</tr>
                </tbody>
            </table></div>
            @if ($ahp)
                <p>CR AHP = {{ $f($ahp->cr) }} ({{ $ahp->cr <= 0.1 ? 'konsisten' : 'tidak konsisten' }})</p>
            @endif

            <h3>2. Matriks Keputusan X</h3>
            <p>K1 = skala nilai guna (1–4), K2 = skala kerahasiaan (1–3), K3 = umur surat (hari), K4 = jumlah akses.</p>
            <div class="rk-table-container"><table class="rk-table">
                <thead><tr><th>Arsip</th>@foreach ($kode as $k)<th>{{ $k }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach ($hitung['matriks'] as $id => $baris)
                        <tr><td>{{ $nomor[$id] }}</td>@foreach ($baris as $x)<td>{{ $f($x, 2) }}</td>@endforeach</tr>
                    @endforeach
                    <tr><td>Pembagi √ΣX²</td>@foreach ($t['pembagi'] as $p)<td>{{ $f($p) }}</td>@endforeach</tr>
                </tbody>
            </table></div>

            <h3>3. Normalisasi Vektor R &nbsp;(r<sub>ij</sub> = x<sub>ij</sub> / √Σx<sub>ij</sub>²)</h3>
            <div class="rk-table-container"><table class="rk-table">
                <thead><tr><th>Arsip</th>@foreach ($kode as $k)<th>{{ $k }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach ($t['normalisasi'] as $id => $baris)
                        <tr><td>{{ $nomor[$id] }}</td>@foreach ($baris as $x)<td>{{ $f($x) }}</td>@endforeach</tr>
                    @endforeach
                </tbody>
            </table></div>

            <h3>4. Normalisasi Terbobot V &nbsp;(v<sub>ij</sub> = w<sub>j</sub> · r<sub>ij</sub>) dan Solusi Ideal</h3>
            <div class="rk-table-container"><table class="rk-table">
                <thead><tr><th>Arsip</th>@foreach ($kode as $k)<th>{{ $k }}</th>@endforeach</tr></thead>
                <tbody>
                    @foreach ($t['terbobot'] as $id => $baris)
                        <tr><td>{{ $nomor[$id] }}</td>@foreach ($baris as $x)<td>{{ $f($x) }}</td>@endforeach</tr>
                    @endforeach
                    <tr><td><strong>A⁺ (ideal positif)</strong></td>@foreach ($t['ideal_positif'] as $x)<td>{{ $f($x) }}</td>@endforeach</tr>
                    <tr><td><strong>A⁻ (ideal negatif)</strong></td>@foreach ($t['ideal_negatif'] as $x)<td>{{ $f($x) }}</td>@endforeach</tr>
                </tbody>
            </table></div>

            <h3>5. Jarak &amp; Kedekatan Relatif &nbsp;(C<sub>i</sub> = D⁻ / (D⁺ + D⁻))</h3>
            <div class="rk-table-container"><table class="rk-table">
                <thead><tr><th>Arsip</th><th>D⁺</th><th>D⁻</th><th>C<sub>i</sub></th><th>Rekomendasi</th></tr></thead>
                <tbody>
                    @foreach ($t['ci'] as $id => $ci)
                        <tr>
                            <td>{{ $nomor[$id] }}</td>
                            <td>{{ $f($t['d_plus'][$id], 6) }}</td>
                            <td>{{ $f($t['d_minus'][$id], 6) }}</td>
                            <td><strong>{{ $f($ci, 6) }}</strong></td>
                            <td>{{ \App\Services\RetensiScoringService::klasifikasikan($ci) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
            <p>Ambang: C<sub>i</sub> ≥ 0,70 = Permanen; 0,40 ≤ C<sub>i</sub> &lt; 0,70 = Simpan Lokal; C<sub>i</sub> &lt; 0,40 = Musnah.</p>
        @endif

        <p><a href="{{ route('retensi-kriteria.index') }}">Kembali ke Kelola Kriteria</a></p>
    </div>
@endsection
