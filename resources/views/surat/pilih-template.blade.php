@extends('layouts.app')

@section('page-title', 'Buat Surat Baru')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    <div class="surat-search">
        <div class="surat-search__field">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
            <input type="text" id="templateSearch" placeholder="Search">
        </div>
    </div>

    <div class="wizard-step">
        <span class="wizard-step__badge">1</span>
        <h2 class="wizard-step__title">Pilih Template</h2>
    </div>

    <div class="template-grid">
        @forelse ($templates as $template)
            <a href="{{ route('surat.form', $template->id) }}"
               class="template-card"
               data-name="{{ strtolower($template->nama_template) }}">
                <div class="template-card__thumb" aria-hidden="true"></div>
                <div class="template-card__name">{{ $template->nama_template }}</div>
            </a>
        @empty
            <div class="template-grid__empty">
                Belum ada template aktif. Hubungi Admin untuk menambahkan template surat.
            </div>
        @endforelse

        <div class="template-grid__empty" id="templateGridEmpty" style="display: none;">
            Template tidak ditemukan.
        </div>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
