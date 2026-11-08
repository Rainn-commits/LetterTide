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


            @php
                $fieldKeys = collect($fields)->map(function ($f) {
                    if (is_array($f)) {
                        return $f['name'] ?? ($f['key'] ?? '');
                    }

                    return is_string($f) ? $f : ($f->name ?? ($f->key ?? ''));
                })->filter()->values()->all();
                $grupSebelumnya = null;
            @endphp

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

                @php
                    $def = \App\Services\SuratFieldResolver::define($key, $fieldKeys);
                    $grup = \App\Services\SuratFieldResolver::group($key);
                    $judulGrup = ($grup && $grup['group'] !== $grupSebelumnya) ? $grup['judul'] : null;
                    $grupSebelumnya = $grup['group'] ?? null;
                    $labelTampil = \App\Services\SuratFieldResolver::label($key) ?? $label;
                @endphp

                @if ($judulGrup)
                    <div style="margin: 20px 0 8px; padding-top: 12px; border-top: 1.5px dashed var(--color-ink); font-size: 13px; font-weight: 800; text-align: left; color: var(--color-ink);">{{ $judulGrup }}</div>
                @endif

                <div class="form-field">
                    <label for="field_{{ Str::slug($key) }}">{{ $labelTampil }}:</label>

                    @if ($def)
                        @include('surat.partials.field-input', ['key' => $key, 'def' => $def, 'value' => $currentValue])
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
