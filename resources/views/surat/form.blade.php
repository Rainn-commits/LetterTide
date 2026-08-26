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
                    if (is_array($field)) {
                        $key = $field['name'] ?? ($field['key'] ?? '');
                        $label = $field['label'] ?? ucwords(str_replace(['_', '-'], ' ', $key));
                        $type = $field['type'] ?? 'text';
                    } else {
                        $key = is_string($field) ? $field : ($field->name ?? ($field->key ?? ''));
                        $label = ucwords(str_replace(['_', '-'], ' ', $key));
                        $type = 'text';
                    }

                    $oldValue = old('fields.' . $key);
                    $lowerKey = strtolower($key);
                    $isPenandatangan = str_contains($lowerKey, 'penandatangan') || str_contains($lowerKey, 'hrd');
                @endphp

                <div class="form-field">
                    <label for="field_{{ Str::slug($key) }}">{{ $label }}:</label>

                    @if ($isPenandatangan)
                        <select 
                            id="field_{{ Str::slug($key) }}" 
                            name="fields[{{ $key }}]" 
                            class="form-control" 
                            required
                        >
                            <option value="">-- Pilih User Penandatangan --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->name }}" {{ $oldValue == $u->name ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>

                    @elseif ($type === 'autocomplete_user' || $key === 'nama_karyawan')
                        <div class="autocomplete">
                            <input
                                type="text"
                                id="field_{{ Str::slug($key) }}"
                                name="fields[{{ $key }}]"
                                value="{{ $oldValue }}"
                                data-autocomplete="user"
                                data-search-url="{{ route('users.search') }}"
                                autocomplete="off"
                                required
                            >
                            <div class="autocomplete__results" id="results_{{ Str::slug($key) }}"></div>
                        </div>

                    @elseif ($type === 'date' || str_contains($lowerKey, 'tanggal'))
                        <input 
                            type="date" 
                            id="field_{{ Str::slug($key) }}" 
                            name="fields[{{ $key }}]" 
                            value="{{ $oldValue }}" 
                            required
                        >

                    @else
                        <input 
                            type="text" 
                            id="field_{{ Str::slug($key) }}" 
                            name="fields[{{ $key }}]" 
                            value="{{ $oldValue }}" 
                            required
                        >
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
