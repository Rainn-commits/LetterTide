@extends('layouts.app')

@section('page-title', 'Edit Isi Surat')

@section('page-css')
    @vite('resources/css/surat-edit.css')
@endsection

@section('content')

    <div class="wizard-step">
        <h2 class="wizard-step__title"> Edit Isi Surat (<span class="nomor-surat-text">{{ $surat->nomor_surat }}</span>)</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('surat.update', $surat->id) }}" class="surat-form" novalidate>
            @csrf
            @method('PUT')


            @forelse ($fields as $field)
                @php
                    $key = is_array($field)
                        ? ($field['name'] ?? ($field['key'] ?? ''))
                        : ($field->name ?? ($field->key ?? ''));

                    $label = is_array($field)
                        ? ($field['label'] ?? ucwords(str_replace(['_', '-'], ' ', $key)))
                        : ($field->label ?? ucwords(str_replace(['_', '-'], ' ', $key)));

                    $type = is_array($field) ? ($field['type'] ?? 'text') : 'text';

                    $currentValue = old('fields.' . $key, $values[$key] ?? '');
                @endphp

                <div class="form-field">
                    <label for="field_{{ Str::slug($key) }}">{{ $key === 'masa_berlaku_penawaran' ? 'Masa Berlaku Penawaran (Hari)' : $label }}:</label>

                    @if (in_array($key, ['ringkasan_fitur', 'platform_aplikasi', 'estimasi_biaya', 'estimasi_waktu_pengerjaan', 'masa_berlaku_penawaran']))
                        @include('surat.partials.field-penawaran', ['key' => $key, 'value' => $currentValue])
                    @elseif ($key === 'paket_layanan')
                        <select
                            id="field_{{ Str::slug($key) }}"
                            name="fields[{{ $key }}]"
                            required
                        >
                            <option value="">Pilih Paket Layanan</option>
                            @foreach (['Basic', 'Standard', 'Premium'] as $paket)
                                <option value="{{ $paket }}"
                                    {{ $currentValue === $paket ? 'selected' : '' }}>
                                    {{ $paket }}
                                </option>
                            @endforeach
                        </select>

                    @elseif ($key === 'biaya_berlangganan')
                        <div class="input-currency">
                            <span>Rp</span>
                            <input
                                type="number"
                                id="field_{{ Str::slug($key) }}"
                                name="fields[{{ $key }}]"
                                value="{{ $currentValue }}"
                                min="0"
                                step="1000"
                                placeholder="Masukkan nominal biaya"
                                required
                            >
                        </div>

                    @elseif (in_array($key, ['tanggal_mulai_layanan', 'tanggal_selesai_layanan']))
                        <input
                            type="date"
                            id="field_{{ Str::slug($key) }}"
                            name="fields[{{ $key }}]"
                            value="{{ $currentValue }}"
                            required
                        >

                    @elseif ($type === 'date' || str_contains(strtolower($key), 'tanggal'))
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

            <div class="form-actions-row">
                <button type="submit" class="btn-block-wide">Simpan Perubahan</button>
                <a href="{{ route('surat.show', $surat->id) }}" class="btn-ghost-pill">Batal</a>
            </div>
        </form>
    </div>

@endsection

@section('page-js')
    @vite('resources/js/surat-wizard.js')
@endsection
