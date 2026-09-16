@extends('layouts.app')

@section('page-title', 'Buat Surat Baru')

@section('page-css')
    @vite('resources/css/surat-form.css')
@endsection

@section('content')

    <div class="wizard-step">
        <span class="wizard-step__badge">2</span>
        <div>
            <h2 class="wizard-step__title">Lengkapi Data Surat</h2>
            <div class="wizard-step__template-name">{{ $template->nama_template }}</div>
        </div>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('surat.store', $template->id) }}" class="surat-form" novalidate>
            @csrf

            <div class="form-field">
                <label for="kop_surat_id">Pilih Kop Surat Perusahaan:</label>
                <select name="kop_surat_id" id="kop_surat_id" class="form-control">
                    <option value="">-- Pilih Kop Surat --</option>
                    @foreach($kopSurats as $kop)
                        <option value="{{ $kop->id }}">
                            {{ $kop->nama_pt }} (Versi {{ $kop->versi }}) ({{ $kop->email }})
                        </option>
                    @endforeach
                </select>
                @error('kop_surat_id')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="ttd_by_user_id">Pilih Penandatangan (Approval Sistem):</label>
                <select name="ttd_by_user_id" id="ttd_by_user_id" class="form-control" autocomplete="off" required>
                    <option value="">-- Pilih User Penandatangan --</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ old('ttd_by_user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
                @error('ttd_by_user_id')
                    <p class="form-field__hint" style="color: var(--color-danger);">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-field">
                <label for="lampiran">Lampiran Surat:</label>
                <input type="text" id="lampiran" name="lampiran" value="{{ old('lampiran', '-') }}" placeholder="Contoh: 1 Lembar / -">
            </div>

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
                    $isDeskripsiPelanggaran = str_contains($lowerKey, 'deskripsi_pelanggaran');

                    $deskripsiPoints = [];

                    if ($isDeskripsiPelanggaran && $oldValue) {
                        $decodedPoints = json_decode($oldValue, true);

                        if (is_array($decodedPoints)) {
                            $deskripsiPoints = array_values(array_filter(
                                $decodedPoints,
                                fn($point) => trim((string) $point) !== ''
                            ));
                        }
                    }

                    if ($isDeskripsiPelanggaran && empty($deskripsiPoints)) {
                        $deskripsiPoints = [''];
                    }
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

                    @elseif ($isDeskripsiPelanggaran)
                        <div
                            class="deskripsi-pelanggaran-input"
                            data-description-field="{{ $key }}"
                        >
                            <input
                                type="hidden"
                                name="fields[{{ $key }}]"
                                id="field_{{ Str::slug($key) }}"
                                value="{{ old('fields.' . $key, json_encode($deskripsiPoints, JSON_UNESCAPED_UNICODE)) }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-description-field]').forEach(function (wrapper) {
                const hiddenInput = wrapper.querySelector('input[type="hidden"]');
                const pointsContainer = wrapper.querySelector('.deskripsi-pelanggaran-points');
                const addButton = wrapper.querySelector('[data-add-point]');

                function updateNumbers() {
                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    points.forEach(function (point, index) {
                        const number = point.querySelector('.deskripsi-pelanggaran-number');
                        const removeButton = point.querySelector('[data-remove-point]');

                        number.textContent = (index + 1) + '.';

                        if (points.length === 1) {
                            removeButton.style.display = 'none';
                        } else {
                            removeButton.style.display = 'inline-flex';
                        }
                    });

                    if (points.length >= 4) {
                        addButton.style.display = 'none';
                    } else {
                        addButton.style.display = 'inline-flex';
                    }
                }

                function syncHiddenInput() {
                    const pointInputs = pointsContainer.querySelectorAll('[data-description-point]');

                    const points = Array.from(pointInputs)
                        .map(function (input) {
                            return input.value.trim();
                        })
                        .filter(function (value) {
                            return value !== '';
                        });

                    hiddenInput.value = JSON.stringify(points);
                }

                function createPoint() {
                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    if (points.length >= 4) {
                        return;
                    }

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
                        >
                            &times;
                        </button>
                    `;

                    pointsContainer.appendChild(point);

                    const newInput = point.querySelector('[data-description-point]');
                    newInput.focus();

                    updateNumbers();
                    syncHiddenInput();
                }

                addButton.addEventListener('click', function () {
                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    if (points.length >= 4) {
                        return;
                    }

                    createPoint();
                });

                pointsContainer.addEventListener('click', function (event) {
                    const removeButton = event.target.closest('[data-remove-point]');

                    if (!removeButton) {
                        return;
                    }

                    const points = pointsContainer.querySelectorAll('.deskripsi-pelanggaran-point');

                    if (points.length <= 1) {
                        return;
                    }

                    removeButton.closest('.deskripsi-pelanggaran-point').remove();

                    updateNumbers();
                    syncHiddenInput();
                });

                pointsContainer.addEventListener('input', function (event) {
                    if (event.target.matches('[data-description-point]')) {
                        syncHiddenInput();
                    }
                });

                wrapper.closest('form').addEventListener('submit', function () {
                    syncHiddenInput();
                });

                updateNumbers();
                syncHiddenInput();
            });
        });
    </script>
@endsection
