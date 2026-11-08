@php
    $fieldId = 'field_' . Str::slug($key);
    $value = (string) ($value ?? '');
    $tipe = $def['type'];
    $opsional = ! empty($def['optional']);
    $wajib = $opsional ? '' : 'required';
    $labelPilih = 'Pilih ' . ($def['label'] ?? ucwords(str_replace('_', ' ', $key)));
@endphp

<div class="field-block" data-field-type="{{ $tipe }}" data-optional="{{ $opsional ? '1' : '0' }}">

@if ($tipe === 'text')
    <input
        type="text"
        id="{{ $fieldId }}"
        name="fields[{{ $key }}]"
        value="{{ $value }}"
        @if (! empty($def['placeholder'])) placeholder="{{ $def['placeholder'] }}" @endif
        {{ $wajib }}
    >

@elseif ($tipe === 'textarea')
    <textarea
        id="{{ $fieldId }}"
        name="fields[{{ $key }}]"
        rows="{{ $def['rows'] ?? 4 }}"
        class="field-textarea"
        @if (! empty($def['placeholder'])) placeholder="{{ $def['placeholder'] }}" @endif
        {{ $wajib }}
    >{{ $value }}</textarea>

@elseif ($tipe === 'rupiah')
    @php
        $nominal = preg_replace('/\D/', '', $value);
    @endphp
    <div class="input-currency">
        <span>Rp</span>
        <input
            type="number"
            id="{{ $fieldId }}"
            name="fields[{{ $key }}]"
            value="{{ $nominal }}"
            min="0"
            step="1000"
            inputmode="numeric"
            placeholder="{{ $def['placeholder'] }}"
            {{ $wajib }}
        >
    </div>

@elseif ($tipe === 'number')
    @php
        $angka = preg_match('/^\s*(\d+)/', $value, $cocokAngka) ? $cocokAngka[1] : '';
    @endphp
    <div class="input-currency">
        <input
            type="number"
            id="{{ $fieldId }}"
            name="fields[{{ $key }}]"
            value="{{ $angka }}"
            min="{{ $def['min'] }}"
            max="{{ $def['max'] }}"
            step="1"
            inputmode="numeric"
            placeholder="{{ $def['placeholder'] }}"
            {{ $wajib }}
        >
        @if (! empty($def['suffix']))
            <span>{{ $def['suffix'] }}</span>
        @endif
    </div>

@elseif ($tipe === 'duration')
    @php
        $angkaDurasi = preg_match('/^\s*(\d+)/', $value, $cocokDurasi) ? $cocokDurasi[1] : '';
        $satuanDurasi = $def['default'];
        foreach ($def['units'] as $satuanOpsi) {
            if (stripos($value, $satuanOpsi) !== false) {
                $satuanDurasi = $satuanOpsi;
                break;
            }
        }
    @endphp
    <input type="hidden" id="{{ $fieldId }}" name="fields[{{ $key }}]" value="{{ $value }}" data-hidden>
    <div class="durasi-row">
        <input type="number" min="1" step="1" inputmode="numeric" placeholder="Contoh: 3" value="{{ $angkaDurasi }}" data-angka>
        <select data-satuan>
            @foreach ($def['units'] as $satuanOpsi)
                <option value="{{ $satuanOpsi }}" {{ $satuanDurasi === $satuanOpsi ? 'selected' : '' }}>{{ $satuanOpsi }}</option>
            @endforeach
        </select>
    </div>

@elseif ($tipe === 'checkbox')
    @php
        $terpilih = array_filter(
            $def['options'],
            fn ($opsi) => preg_match('/\b' . preg_quote($opsi, '/') . '\b/i', $value) === 1
        );
    @endphp
    <input type="hidden" id="{{ $fieldId }}" name="fields[{{ $key }}]" value="{{ $value }}" data-hidden>
    <div class="platform-options">
        @foreach ($def['options'] as $opsi)
            <label class="platform-option">
                <input type="checkbox" value="{{ $opsi }}" data-option {{ in_array($opsi, $terpilih, true) ? 'checked' : '' }}>
                <span>{{ $opsi }}</span>
            </label>
        @endforeach
    </div>

@elseif ($tipe === 'select')
    <select id="{{ $fieldId }}" name="fields[{{ $key }}]" {{ $wajib }}>
        <option value="">{{ $labelPilih }}</option>
        @foreach ($def['options'] as $opsi)
            <option value="{{ $opsi }}" {{ $value === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
        @endforeach
    </select>

@elseif ($tipe === 'select_other')
    @php
        $opsiTetap = array_values(array_filter($def['options'], fn ($o) => $o !== 'Lainnya'));
        $cocokOpsi = in_array($value, $opsiTetap, true);
        $pakaiLainnya = $value !== '' && ! $cocokOpsi;
    @endphp
    <input type="hidden" id="{{ $fieldId }}" name="fields[{{ $key }}]" value="{{ $value }}" data-hidden>
    <select data-select>
        <option value="">{{ $labelPilih }}</option>
        @foreach ($opsiTetap as $opsi)
            <option value="{{ $opsi }}" {{ $cocokOpsi && $value === $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
        @endforeach
        <option value="__lainnya" {{ $pakaiLainnya ? 'selected' : '' }}>Lainnya</option>
    </select>
    <input
        type="text"
        class="select-other"
        data-other
        placeholder="Tulis jenis lainnya"
        value="{{ $pakaiLainnya ? $value : '' }}"
        {{ $pakaiLainnya ? '' : 'hidden' }}
    >

@elseif ($tipe === 'month')
    <input
        type="month"
        id="{{ $fieldId }}"
        name="fields[{{ $key }}]"
        value="{{ $value }}"
        class="field-month"
        {{ $wajib }}
    >

@elseif ($tipe === 'date_range')
    @php
        $mulaiRentang = '';
        $selesaiRentang = '';
        if (preg_match('/^(\d{4}-\d{2}-\d{2}) s\.d\. (\d{4}-\d{2}-\d{2})$/', $value, $cocokRentang)) {
            $mulaiRentang = $cocokRentang[1];
            $selesaiRentang = $cocokRentang[2];
        }
    @endphp
    <input type="hidden" id="{{ $fieldId }}" name="fields[{{ $key }}]" value="{{ $value }}" data-hidden>
    <div class="range-row">
        <input type="date" value="{{ $mulaiRentang }}" data-mulai aria-label="Tanggal mulai">
        <span>s.d.</span>
        <input type="date" value="{{ $selesaiRentang }}" data-selesai aria-label="Tanggal selesai">
    </div>
@endif

@if (! empty($def['helper']))
    <p class="field-help">{{ $def['helper'] }}</p>
@endif

@if (in_array($tipe, ['duration', 'checkbox', 'select_other', 'date_range'], true))
    <p class="field-error" data-error hidden>{{ $def['error'] ?? 'Lengkapi isian ini.' }}</p>
@endif

</div>

@once
    <style>
        .field-block [hidden] {
            display: none !important;
        }

        .field-textarea {
            width: 100%;
            min-height: 60px;
            box-sizing: border-box;
            padding: 10px 14px;
            border: 1.5px solid var(--color-ink);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            line-height: 1.5;
            color: var(--color-ink);
            background: var(--color-white);
            text-align: left;
            resize: vertical;
            transition: border-color 0.15s ease;
        }

        .field-textarea:focus {
            outline: none;
            border-color: var(--color-primary);
        }

        .field-textarea::placeholder,
        .field-block input::placeholder {
            color: #94a3b8;
        }

        .field-help {
            margin-top: 5px;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
            text-align: justify !important;
            text-align-last: left;
        }

        .field-error {
            margin-top: 5px;
            font-size: 12px;
            color: var(--color-danger);
            text-align: left;
        }

        .field-block input[type="month"],
        .field-block input.select-other {
            width: 100%;
            box-sizing: border-box;
            height: 40px;
            padding: 9px 14px;
            border: 1.5px solid var(--color-ink);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            color: var(--color-ink);
            background: var(--color-white);
            text-align: left;
        }

        .field-block input.select-other {
            margin-top: 8px;
        }

        .field-block input[type="month"]:focus,
        .field-block input.select-other:focus {
            outline: none;
            border-color: var(--color-primary);
        }

        .form-field .durasi-row,
        .form-field .range-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-field .durasi-row input[type="number"],
        .form-field .range-row input[type="date"] {
            flex: 1 1 auto !important;
            width: auto !important;
            min-width: 0;
            box-sizing: border-box;
            height: 40px;
            padding: 9px 14px;
            border: 1.5px solid var(--color-ink);
            border-radius: var(--radius-sm);
            font-size: 14px;
            text-align: left;
        }

        .form-field .durasi-row select {
            flex: 0 0 130px !important;
            width: 130px !important;
            height: 40px;
            box-sizing: border-box;
            padding: 0 10px;
            border: 1.5px solid var(--color-ink);
            border-radius: var(--radius-sm);
            background: var(--color-white);
            font-size: 14px;
        }

        .range-row > span {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-ink);
        }

        .platform-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-field .platform-options .platform-option {
            display: flex !important;
            align-items: center;
            gap: 8px;
            width: auto !important;
            margin: 0 !important;
            padding: 8px 16px;
            border: 1.5px solid var(--color-ink);
            border-radius: var(--radius-sm);
            background: var(--color-white);
            font-size: 14px;
            font-weight: 600 !important;
            cursor: pointer;
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .platform-option input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: var(--color-primary);
            cursor: pointer;
        }

        .platform-option:has(input:checked) {
            border-color: var(--color-primary);
            background: #f0fdfe;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function gabung(daftar) {
                if (daftar.length <= 1) {
                    return daftar.join('');
                }

                if (daftar.length === 2) {
                    return daftar.join(' dan ');
                }

                return daftar.slice(0, -1).join(', ') + ', dan ' + daftar[daftar.length - 1];
            }

            document.querySelectorAll('.field-block').forEach(function (blok) {
                const tipe = blok.dataset.fieldType;
                const opsional = blok.dataset.optional === '1';
                const hidden = blok.querySelector('[data-hidden]');
                const error = blok.querySelector('[data-error]');
                const form = blok.closest('form');

                if (!hidden) {
                    return;
                }

                let fokus = null;
                let baca = null;

                if (tipe === 'checkbox') {
                    const opsi = Array.from(blok.querySelectorAll('[data-option]'));
                    fokus = opsi[0];
                    baca = function () {
                        return gabung(opsi.filter(function (o) { return o.checked; })
                            .map(function (o) { return o.value; }));
                    };
                    opsi.forEach(function (o) { o.addEventListener('change', sinkron); });
                } else if (tipe === 'duration') {
                    const angka = blok.querySelector('[data-angka]');
                    const satuan = blok.querySelector('[data-satuan]');
                    fokus = angka;
                    baca = function () {
                        const n = parseInt(angka.value, 10);

                        return (!isNaN(n) && n >= 1) ? n + ' ' + satuan.value : '';
                    };
                    angka.addEventListener('input', sinkron);
                    satuan.addEventListener('change', sinkron);
                } else if (tipe === 'select_other') {
                    const pilih = blok.querySelector('[data-select]');
                    const lain = blok.querySelector('[data-other]');
                    fokus = pilih;
                    baca = function () {
                        if (pilih.value === '__lainnya') {
                            lain.hidden = false;

                            return lain.value.trim();
                        }

                        lain.hidden = true;

                        return pilih.value;
                    };
                    pilih.addEventListener('change', sinkron);
                    lain.addEventListener('input', sinkron);
                } else if (tipe === 'date_range') {
                    const mulai = blok.querySelector('[data-mulai]');
                    const selesai = blok.querySelector('[data-selesai]');
                    fokus = mulai;
                    baca = function () {
                        selesai.min = mulai.value || '';

                        if (!mulai.value || !selesai.value || selesai.value < mulai.value) {
                            return '';
                        }

                        return mulai.value + ' s.d. ' + selesai.value;
                    };
                    mulai.addEventListener('change', sinkron);
                    selesai.addEventListener('change', sinkron);
                } else {
                    return;
                }

                function sinkron() {
                    hidden.value = baca();

                    if (hidden.value !== '' && error) {
                        error.hidden = true;
                    }

                    return hidden.value !== '';
                }

                if (form) {
                    form.addEventListener('submit', function (event) {
                        const terisi = sinkron();

                        if (!terisi && !opsional) {
                            event.preventDefault();

                            if (error) {
                                error.hidden = false;
                            }

                            if (!event.__fokusSudah && fokus) {
                                fokus.focus();
                                event.__fokusSudah = true;
                            }
                        }
                    });
                }

                sinkron();
            });

            document.querySelectorAll('input[type="date"][id^="field_tanggal-selesai-"], input[type="date"][id="field_tanggal-target-selesai"]')
                .forEach(function (selesai) {
                    const kandidat = selesai.id === 'field_tanggal-target-selesai'
                        ? ['field_tanggal-mulai-pekerjaan', 'field_tanggal-mulai-proyek']
                        : [selesai.id.replace('field_tanggal-selesai-', 'field_tanggal-mulai-')];

                    kandidat.forEach(function (idMulai) {
                        const mulai = document.getElementById(idMulai);

                        if (!mulai || mulai === selesai) {
                            return;
                        }

                        function batasi() {
                            selesai.min = mulai.value || '';
                        }

                        mulai.addEventListener('change', batasi);
                        batasi();
                    });
                });
        });
    </script>
@endonce
