@extends('layouts.app')

@section('page-title', 'Kelola Kriteria Retensi')

@section('page-css')
    @vite('resources/css/retensi-kriteria.css')
@endsection

@section('content')
<div class="rk-page">
    <h2 class="rk-page__title">Kelola Kriteria Retensi Arsip</h2>
    <p class="rk-page__subtitle">Atur bobot tiap kriteria yang digunakan untuk menghitung skor evaluasi retensi arsip (metode SAW).</p>

    @if (session('success'))
        <div class="rk-alert {{ str_contains(session('success'), 'disarankan') ? 'rk-alert--warning' : 'rk-alert--success' }}">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rk-alert rk-alert--error">
            {{ $errors->first() }}
        </div>
    @endif

    @php
        $totalBobot = $kriteria->sum('bobot');
        $isTotalIdeal = round($totalBobot, 2) == 1.0;
    @endphp

    <div class="rk-total-bobot {{ $isTotalIdeal ? 'rk-total-bobot--ok' : 'rk-total-bobot--warn' }}">
        Total bobot saat ini: {{ number_format($totalBobot, 2) }}
        {{ $isTotalIdeal ? '(ideal)' : '(disarankan = 1.0)' }}
    </div>

    <table class="rk-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Kriteria</th>
                <th>Tipe</th>
                <th>Bobot Saat Ini</th>
                <th>Ubah Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kriteria as $k)
                <tr>
                    <td>{{ $k->kode }}</td>
                    <td>{{ $k->nama_kriteria }}</td>
                    <td>
                        <span class="rk-tipe-badge rk-tipe-badge--{{ $k->tipe }}">
                            {{ ucfirst($k->tipe) }}
                        </span>
                    </td>
                    <td>{{ number_format($k->bobot, 2) }}</td>
                    <td>
                        <form class="rk-bobot-form" method="POST" action="{{ route('retensi-kriteria.update', $k->id) }}">
                            @csrf
                            @method('PUT')
                            <input 
                                type="number" 
                                name="bobot" 
                                value="{{ old('bobot', $k->bobot) }}" 
                                step="0.01" 
                                min="0" 
                                max="1" 
                                required
                            >
                            <button type="submit">Simpan</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

