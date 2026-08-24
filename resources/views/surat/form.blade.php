@extends('layouts.app')

@section('page-title', 'Buat Surat Baru')

@section('page-css')
    @vite('resources/css/surat.css')
@endsection

@section('content')

    <div class="wizard-step">
        <span class="wizard-step__badge">2</span>
        <h2 class="wizard-step__title">Lengkapi Data Surat</h2>
    </div>

    @error('kop_surat')
        <div class="surat-alert surat-alert--error">{{ $message }}</div>
    @enderror

    <div class="card">
        <form method="POST" action="{{ route('surat.store', $template->id) }}" class="surat-form" novalidate>
            @csrf

            @forelse ($fields as $field)
                @php
                    $key   = is_string($field) ? $field : (is_array($field) ? ($field['key'] ?? '') : ($field->key ?? ''));
                    $label = is_string($field) ? ucwords(str_replace('_', ' ', $field)) : (is_array($field) ? ($field['label'] ?? $key) : ($field->label ?? $key));
                    
                    $isDate = str_contains($key, 'tanggal');
                    $isEmployeeName = $key === 'nama_karyawan';
                    $oldValue = old('fields.' . $key);
                @endphp

                <div class="form-field">
                    <label for="field_{{ $key }}">{{ $label }}:</label>

                    @if ($isEmployeeName)
                        <div class="autocomplete">
                            <input
                                type="text"
                                id="field_{{ $key }}"
                                name="fields[{{ $key }}]"
                                value="{{ $oldValue }}"
                                data-autocomplete="user"
                                data-search-url="{{ route('users.search') }}"
                                autocomplete="off"
                                required
                            >
                            <div class="autocomplete__results" id="results_{{ $key }}"></div>
                        </div>
                    @elseif ($isDate)
                        <input type="date" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" required>
                    @else
                        <input type="text" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" required>
                    @endif

                    @error('fields.' . $key)
                        <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                    @enderror
                </div>
            @empty
                <p class="form-field__hint" style="margin-bottom: 20px;">
                    Template ini tidak punya data yang perlu diisi. Klik tombol di bawah untuk langsung membuat suratnya.
                </p>
            @endforelse

            <button type="submit" class="btn-block-wide">Buat Nomor Surat &amp; Simpan</button>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
