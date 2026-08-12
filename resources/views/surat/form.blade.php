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

    <div class="card">
        <form method="POST" action="{{ route('surat.store', $template->id) }}" class="surat-form" novalidate>
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">

            @foreach ($fields as $field)
                @php
                    $key = is_array($field) ? $field['key'] : $field->key;
                    $label = is_array($field) ? $field['label'] : $field->label;
                    $isDate = str_contains($key, 'tanggal');
                    $isEmployeeName = $key === 'nama_karyawan';
                    $oldValue = old('jawaban.' . $key);
                @endphp

                <div class="form-field">
                    <label for="field_{{ $key }}">{{ $label }}:</label>

                    @if ($isEmployeeName)
                        <div class="autocomplete">
                            <input
                                type="text"
                                id="field_{{ $key }}"
                                name="jawaban[{{ $key }}]"
                                value="{{ $oldValue }}"
                                data-autocomplete="user"
                                data-search-url="{{ route('users.search') }}"
                                autocomplete="off"
                                required
                            >
                            <div class="autocomplete__results" id="results_{{ $key }}"></div>
                        </div>
                    @elseif ($isDate)
                        <input type="date" id="field_{{ $key }}" name="jawaban[{{ $key }}]" value="{{ $oldValue }}" required>
                    @else
                        <input type="text" id="field_{{ $key }}" name="jawaban[{{ $key }}]" value="{{ $oldValue }}" required>
                    @endif

                    @error('jawaban.' . $key)
                        <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="btn-block-wide">Buat Nomor Surat &amp; Simpan</button>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
