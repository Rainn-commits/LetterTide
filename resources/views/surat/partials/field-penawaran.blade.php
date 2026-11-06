@php
    $fieldId = 'field_' . Str::slug($key);
    $value = (string) ($value ?? '');
@endphp

@if ($key === 'ringkasan_fitur')
    <textarea
        id="{{ $fieldId }}"
        name="fields[{{ $key }}]"
        rows="7"
        class="field-textarea"
        placeholder="Contoh:&#10;- Login dan registrasi pengguna&#10;- Dashboard ringkasan data&#10;- Pengelolaan data produk&#10;- Laporan penjualan bulanan"
        required
    >{{ $value }}</textarea>
    <p class="field-help">
        Tuliskan fitur-fitur utama aplikasi yang akan dikembangkan, misalnya login pengguna, dashboard, pengelolaan data, dan laporan.
    </p>

@elseif ($key === 'platform_aplikasi')
    @php
        $opsiPlatform = ['Android', 'iOS', 'Web'];
        $platformDipilih = array_filter(
            $opsiPlatform,
            fn ($p) => preg_match('/\b' . preg_quote($p, '/') . '\b/i', $value) === 1
        );
    @endphp

    <div class="platform-field" data-platform-field>
        <input
            type="hidden"
            id="{{ $fieldId }}"
            name="fields[{{ $key }}]"
            value="{{ $value }}"
            data-platform-value
        >

        <div class="platform-options">
            @foreach ($opsiPlatform as $platform)
                <label class="platform-option">
                    <input
                        type="checkbox"
                        value="{{ $platform }}"
                        data-platform-option
                        {{ in_array($platform, $platformDipilih, true) ? 'checked' : '' }}
                    >
                    <span>{{ $platform }}</span>
                </label>
            @endforeach
        </div>

        <p class="field-help">
            Tentukan platform yang menjadi target pengembangan aplikasi. Bisa pilih lebih dari satu, misalnya Android dan iOS.
        </p>
        <p class="field-error" data-platform-error hidden>Pilih minimal satu platform.</p>
    </div>

@elseif (in_array($key, ['estimasi_biaya', 'biaya_berlangganan']))
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
            placeholder="Contoh: 5000000"
            required
        >
    </div>
    <p class="field-help">
        Masukkan nominal biaya dalam rupiah, tanpa titik atau koma. Contoh: 5000000 akan tampil sebagai Rp 5.000.000 di surat.
    </p>

@elseif ($key === 'estimasi_waktu_pengerjaan')
    @php
        $satuanOpsi = ['Hari', 'Minggu', 'Bulan'];
        $angkaWaktu = preg_match('/^\s*(\d+)/', $value, $m1) ? $m1[1] : '';
        $satuanWaktu = 'Bulan';
        foreach ($satuanOpsi as $s) {
            if (stripos($value, $s) !== false) {
                $satuanWaktu = $s;
                break;
            }
        }
    @endphp

    <div class="durasi-field" data-durasi-field>
        <input type="hidden" id="{{ $fieldId }}" name="fields[{{ $key }}]" value="{{ $value }}" data-durasi-value>

        <div class="durasi-row">
            <input type="number" min="1" step="1" inputmode="numeric" placeholder="Contoh: 3" value="{{ $angkaWaktu }}" data-durasi-angka>
            <select data-durasi-satuan>
                @foreach ($satuanOpsi as $s)
                    <option value="{{ $s }}" {{ $satuanWaktu === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <p class="field-help">
            Isi lama pengerjaan proyek sejak kesepakatan, lalu pilih satuannya. Contoh: 3 Bulan atau 12 Minggu.
        </p>
        <p class="field-error" data-durasi-error hidden>Isi lama pengerjaan dengan angka minimal 1.</p>
    </div>

@elseif ($key === 'masa_berlaku_penawaran')
    @php
        $hari = preg_match('/^\s*(\d+)/', $value, $cocok) ? $cocok[1] : '';
    @endphp

    <div class="input-currency">
        <input
            type="number"
            id="{{ $fieldId }}"
            name="fields[{{ $key }}]"
            value="{{ $hari }}"
            min="1"
            max="365"
            step="1"
            inputmode="numeric"
            placeholder="Contoh: 30"
            required
        >
        <span>hari</span>
    </div>
    <p class="field-help">
        Masukkan jumlah hari berlakunya penawaran sejak tanggal surat diterbitkan.
    </p>
@endif

@once
    <style>
        .field-textarea {
            width: 100%;
            min-height: 150px;
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

        .field-textarea::placeholder {
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

        .durasi-row {
            display: flex;
            gap: 10px;
        }

        .form-field .durasi-row {
            display: flex;
            gap: 10px;
        }

        .form-field .durasi-row input[type="number"] {
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
            document.querySelectorAll('[data-platform-field]').forEach(function (wrapper) {
                const hidden = wrapper.querySelector('[data-platform-value]');
                const options = Array.from(wrapper.querySelectorAll('[data-platform-option]'));
                const error = wrapper.querySelector('[data-platform-error]');
                const form = wrapper.closest('form');

                function gabung(daftar) {
                    if (daftar.length <= 1) {
                        return daftar.join('');
                    }

                    if (daftar.length === 2) {
                        return daftar.join(' dan ');
                    }

                    return daftar.slice(0, -1).join(', ') + ', dan ' + daftar[daftar.length - 1];
                }

                function sinkron() {
                    const dipilih = options
                        .filter(function (opsi) { return opsi.checked; })
                        .map(function (opsi) { return opsi.value; });

                    hidden.value = gabung(dipilih);

                    if (dipilih.length > 0) {
                        error.hidden = true;
                    }

                    return dipilih.length;
                }

                options.forEach(function (opsi) {
                    opsi.addEventListener('change', sinkron);
                });

                if (form) {
                    form.addEventListener('submit', function (event) {
                        if (sinkron() === 0) {
                            event.preventDefault();
                            error.hidden = false;
                            options[0].focus();
                        }
                    });
                }

                sinkron();
            });

            document.querySelectorAll('[data-durasi-field]').forEach(function (wrapper) {
                const hidden = wrapper.querySelector('[data-durasi-value]');
                const angka = wrapper.querySelector('[data-durasi-angka]');
                const satuan = wrapper.querySelector('[data-durasi-satuan]');
                const error = wrapper.querySelector('[data-durasi-error]');
                const form = wrapper.closest('form');

                function sinkron() {
                    const n = parseInt(angka.value, 10);
                    const valid = !isNaN(n) && n >= 1;

                    hidden.value = valid ? n + ' ' + satuan.value : '';

                    if (valid) {
                        error.hidden = true;
                    }

                    return valid;
                }

                angka.addEventListener('input', sinkron);
                satuan.addEventListener('change', sinkron);

                if (form) {
                    form.addEventListener('submit', function (event) {
                        if (!sinkron()) {
                            event.preventDefault();
                            error.hidden = false;
                            angka.focus();
                        }
                    });
                }

                sinkron();
            });
        });
    </script>
@endonce
