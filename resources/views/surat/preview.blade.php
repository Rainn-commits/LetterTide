@extends('layouts.app')

@section('page-title', 'Preview Surat')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    @if (session('success'))
        <div class="surat-alert surat-alert--success">{{ session('success') }}</div>
    @endif

    <div class="wizard-step">
        <h2 class="wizard-step__title">Preview Surat</h2>
    </div>

    <div class="surat-preview-toolbar" style="display: flex; gap: 10px; align-items: center;">
        <a href="{{ route('surat.create') }}" class="btn-ghost">&larr; Buat Surat Lain</a>
        
        @if (Route::has('surat.edit'))
            <a href="{{ route('surat.edit', $surat->id) }}" class="btn-dark" style="display: inline-flex; align-items: center; gap: 8px; background-color: #f59e0b; color: #ffffff; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Isi Surat
            </a>
        @endif

        <button type="button" class="btn-dark" data-print-trigger style="display: inline-flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 9V3h12v6" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <path d="M6 18H4a1 1 0 01-1-1v-6a1 1 0 011-1h16a1 1 0 011 1v6a1 1 0 01-1 1h-2" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                <rect x="6" y="14" width="12" height="7" stroke="currentColor" stroke-width="1.6"/>
            </svg>
            Cetak
        </button>
    </div>

    <div class="surat-paper" id="suratPaper">

        <div class="surat-paper__kop">
            @if ($surat->kopSurat?->logo)
                <img src="{{ asset('storage/' . $surat->kopSurat->logo) }}" alt="Logo" class="surat-paper__logo">
            @endif
            <div class="surat-paper__company">
                <strong>{{ $surat->kopSurat?->nama_pt ?? 'PT Inovindo Digital Media' }}</strong>
                <span>{{ $surat->kopSurat?->alamat_pt ?? '' }}</span>
                @if ($surat->kopSurat && ($surat->kopSurat->telepon || $surat->kopSurat->email || $surat->kopSurat->website))
                    <span class="surat-paper__contact">
                        {!! collect([
                            $surat->kopSurat->telepon ? 'Telp: ' . e($surat->kopSurat->telepon) : null,
                            e($surat->kopSurat->email),
                            e($surat->kopSurat->website),
                        ])->filter()->implode(' &middot; ') !!}
                    </span>
                @endif
            </div>
        </div>

        <hr class="surat-paper__divider">

        <div class="surat-paper__meta">
            <span>Nomor: {{ $surat->nomor_surat }}</span>
            <span>{{ $surat->tanggal_surat?->translatedFormat('d F Y') }}</span>
        </div>

        <div class="surat-paper__title">{{ $surat->template?->nama_template }}</div>

        <div class="surat-paper__body" style="white-space: pre-line; line-height: 1.6; text-align: justify;">
            {!! $isiSuratRendered !!}
        </div>

        <div class="surat-paper__signature">
            <span>{{ $surat->kopSurat?->nama_pt ?? 'PT Inovindo Digital Media' }},</span>
            <span>{{ $surat->tanggal_surat?->translatedFormat('d F Y') }}</span>
            <div class="surat-paper__signature-space"></div>
            
            @php
                $values = $surat->isiSurat->pluck('nilai_field', 'nama_field')->toArray();
                $penandatangan = $values['Penandatangan / HRD / Supervisor'] 
                            ?? $values['Penandatangan'] 
                            ?? $values['Nama Penandatangan'] 
                            ?? $surat->user?->name;
            @endphp

            <strong>{{ $penandatangan }}</strong>
        </div>

    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
