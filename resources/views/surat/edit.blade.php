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

                    $lowerKey = strtolower($key);

                    $isPenandatangan = str_contains($lowerKey, 'penandatangan')
                        || str_contains($lowerKey, 'hrd')
                        || str_contains($lowerKey, 'nama_direktur');

                    $contactPhoneField = match ($key) {
                        'nama_guru_pembimbing' => 'kontak_guru_pembimbing',
                        'nama_dosen_pembimbing' => 'kontak_dosen',
                        'nama_atasan_langsung' => 'kontak_atasan_langsung',
                        'anggota_1_nama' => 'anggota_1_kontak',
                        'anggota_2_nama' => 'anggota_2_kontak',
                        'anggota_3_nama' => 'anggota_3_kontak',
                        default => null,
                    };

                    $isDeskripsiPelanggaran = str_contains($lowerKey, 'deskripsi_pelanggaran');

                    $deskripsiPoints = [];

                    if ($isDeskripsiPelanggaran && $currentValue) {
                        $decodedPoints = json_decode($currentValue, true);

                        if (is_array($decodedPoints)) {
                            $deskripsiPoints = array_values(array_filter(
                                $decodedPoints,
                                fn($point) => trim((string) $point) !== ''
                            ));
                        } else {
                            $deskripsiPoints = array_values(array_filter(
                                preg_split('/\r\n|\r|\n/', $currentValue),
                                fn($point) => trim($point) !== ''
                            ));
                        }
                    }

                    if ($isDeskripsiPelanggaran && empty($deskripsiPoints)) {
                        $deskripsiPoints = [''];
                    }

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
                        @include('surat.partials.field-input', [
                            'key' => $key,
                            'def' => $def,
                            'value' => $currentValue
                        ])

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

                    @elseif ($isPenandatangan)
                        <input
                            type="text"
                            id="field_{{ Str::slug($key) }}"
                            name="fields[{{ $key }}]"
                            value="Novi Setia Nurviat"
                            readonly
                        >

                    @elseif ($type === 'autocomplete_user' || $key === 'nama_karyawan')
                        <div class="autocomplete">
                            <input
                                type="text"
                                id="field_{{ Str::slug($key) }}"
                                name="fields[{{ $key }}]"
                                value="{{ $currentValue }}"
                                data-autocomplete="user"
                                data-search-url="{{ route('users.search') }}"
                                autocomplete="off"
                                required
                            >
                            <div class="autocomplete__results" id="results_{{ Str::slug($key) }}"></div>
                        </div>

                    @elseif ($contactPhoneField)
                        <div class="autocomplete">
                            <input
                                type="text"
                                id="field_{{ Str::slug($key) }}"
                                name="fields[{{ $key }}]"
                                value="{{ $currentValue }}"
                                data-autocomplete="kontak"
                                data-search-url="{{ route('kontak.search') }}"
                                data-phone-field="{{ $contactPhoneField }}"
                                autocomplete="off"
                                required
                            >
                            <div
                                class="autocomplete__results"
                                id="results_{{ Str::slug($key) }}"
                            ></div>
                        </div>

                    @elseif ($type === 'date' || str_contains($lowerKey, 'tanggal'))
                        <input
                            type="date"
                            id="field_{{ Str::slug($key) }}"
                            name="fields[{{ $key }}]"
                            value="{{ $currentValue }}"
                            required
                        >

                    @elseif ($key === 'tingkat_sp')
                        <div style="display: flex; gap: 32px; align-items: center; flex-direction: row;">
                            @foreach ([1, 2, 3] as $tingkat)
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input
                                        type="radio"
                                        name="fields[{{ $key }}]"
                                        value="{{ $tingkat }}"
                                        {{ (string) $currentValue === (string) $tingkat ? 'checked' : '' }}
                                        required
                                    >
                                    SP {{ $tingkat }}
                                </label>
                            @endforeach
                        </div>

                    @elseif ($isDeskripsiPelanggaran)
                        <div
                            class="deskripsi-pelanggaran-input"
                            data-description-field="{{ $key }}"
                        >
                            <input
                                type="hidden"
                                name="fields[{{ $key }}]"
                                id="field_{{ Str::slug($key) }}"
                                value="{{ json_encode($deskripsiPoints, JSON_UNESCAPED_UNICODE) }}"
                            >

                            <div class="deskripsi-pelanggaran-points">
                                @foreach ($deskripsiPoints as $index => $point)
                                    <div class="deskripsi-pelanggaran-point">
                                        <span class="deskripsi-pelanggaran-number">{{ $index + 1 }}.</span>

                                        <input
                                            type="text"
                                            value="{{ $point }}"
                                            data-description-point
                                            placeholder="Tuliskan poin pelanggaran..."
                                            required
                                        >

                                        <button
                                            type="button"
                                            class="deskripsi-pelanggaran-remove"
                                            data-remove-point
                                            title="Hapus poin"
                                            @if (count($deskripsiPoints) === 1) style="display: none;" @endif
                                        >
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button
                                type="button"
                                class="deskripsi-pelanggaran-add"
                                data-add-point
                            >
                                + Tambah Poin
                            </button>
                        </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-description-field]').forEach(function (wrapper) {
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                const pointsContainer = wrapper.querySelector('.deskripsi-pelanggaran-points');
                const addButton = wrapper.querySelector('[data-add-point]');

                function updateNumbers() {
                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    points.forEach(function (point, index) {
                        point.querySelector('.deskripsi-pelanggaran-number').textContent = (index + 1) + '.';

                        const removeButton = point.querySelector('[data-remove-point]');
                        removeButton.style.display = points.length === 1 ? 'none' : 'inline-flex';
                    });

                    addButton.style.display = points.length >= 10 ? 'none' : 'inline-flex';
                }

                function syncHiddenInput() {
                    const pointInputs = pointsContainer.querySelectorAll('[data-description-point]');

                    const points = Array.from(pointInputs)
                        .map(input => input.value.trim())
                        .filter(value => value !== '');

                    hiddenInput.value = JSON.stringify(points);
                }

                function createPoint() {
                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    if (points.length >= 10) return;

                    const point = document.createElement('div');
                    point.className = 'deskripsi-pelanggaran-point';

                    point.innerHTML = `
                        <span class="deskripsi-pelanggaran-number"></span>
                        <input
                            type="text"
                            data-description-point
                            placeholder="Tuliskan poin pelanggaran..."
                            required
                        >
                        <button
                            type="button"
                            class="deskripsi-pelanggaran-remove"
                            data-remove-point
                            title="Hapus poin"
                        >&times;</button>
                    `;

                    pointsContainer.appendChild(point);
                    point.querySelector('[data-description-point]').focus();

                    updateNumbers();
                    syncHiddenInput();
                }

                addButton.addEventListener('click', createPoint);

                pointsContainer.addEventListener('click', function (event) {
                    const removeButton = event.target.closest('[data-remove-point]');

                    if (!removeButton) return;

                    if (pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point').length <= 1) return;

                    removeButton.closest('.deskripsi-pelanggaran-point').remove();

                    updateNumbers();
                    syncHiddenInput();
                });

                pointsContainer.addEventListener('input', function (event) {
                    if (event.target.matches('[data-description-point]')) {
                        syncHiddenInput();
                    }
                });

                wrapper.closest('form').addEventListener('submit', syncHiddenInput);

                updateNumbers();
                syncHiddenInput();
            });
        });
    </script>
@endsection

