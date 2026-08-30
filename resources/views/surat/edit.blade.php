@extends('layouts.app')

@section('page-title', 'Edit Isi Surat')

@section('page-css')
    @vite('resources/css/surat-form.css')
@endsection

@section('content')

    <div class="wizard-step">
        <h2 class="wizard-step__title">Edit Isi Surat ({{ $surat->nomor_surat }})</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('surat.update', $surat->id) }}" class="surat-form" novalidate>
            @csrf
            @method('PUT')

            @forelse ($fields as $field)
                @php
                    $key = is_array($field) ? ($field['name'] ?? '') : ($field->name ?? '');
                    $label = is_array($field) ? ($field['label'] ?? '') : ($field->label ?? ucwords(str_replace('_', ' ', $key)));
                    $type = is_array($field) ? ($field['type'] ?? 'text') : 'text';

                    $currentValue = old('fields.' . $key, $values[$key] ?? '');
                @endphp

                <div class="form-field">
                    <label for="field_{{ Str::slug($key) }}">{{ $label }}:</label>

                    @if ($type === 'date' || str_contains(strtolower($key), 'tanggal'))
                        <input 
                            type="date" 
                            id="field_{{ Str::slug($key) }}" 
                            name="fields[{{ $key }}]" 
                            value="{{ $currentValue }}" 
                            required
                        >
                    @else
                        <input 
                            type="text" 
                            id="field_{{ Str::slug($key) }}" 
                            name="fields[{{ $key }}]" 
                            value="{{ $currentValue }}" 
                            required
                        >
                    @endif

                    @error('fields.' . $key)
                        <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                    @enderror
                </div>
            @empty
                <p class="form-field__hint">Tidak ada bidang isi surat yang dapat diubah.</p>
            @endforelse

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn-block-wide" style="flex: 1;">Simpan Perubahan</button>
                <a href="{{ route('surat.show', $surat->id) }}" class="btn-ghost" style="text-decoration: none; text-align: center; line-height: 2.5;">Batal</a>
            </div>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
