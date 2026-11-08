<?php

namespace App\Services;

class SuratFieldResolver
{
    private const HELPER_RUPIAH = 'Masukkan nominal dalam rupiah, tanpa titik atau koma. Contoh: 5000000 akan tampil sebagai Rp 5.000.000 di surat.';

    private const GRUP_JUDUL = [
        'item'    => 'Item',
        'barang'  => 'Barang/Jasa',
        'layanan' => 'Layanan',
        'peserta' => 'Peserta',
        'anggota' => 'Anggota Tim',
    ];

    private const GRUP_LABEL = [
        'nama'       => 'Nama',
        'jumlah'     => 'Jumlah',
        'harga'      => 'Harga Satuan',
        'biaya'      => 'Biaya per Bulan',
        'keterangan' => 'Keterangan',
        'status'     => 'Status',
        'spesifikasi' => 'Spesifikasi',
        'peran'      => 'Peran/Posisi',
        'kontak'     => 'Kontak',
        'divisi'     => 'Divisi',
        'sesi'       => 'Sesi',
    ];

    private const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public static function group(string $key): ?array
    {
        if (! preg_match('/^(item|barang|layanan|peserta|anggota)_(\d+)_([a-z]+)$/', $key, $m)) {
            return null;
        }

        $n = (int) $m[2];

        return [
            'group'  => $m[1] . '_' . $n,
            'prefix' => $m[1],
            'n'      => $n,
            'suffix' => $m[3],
            'judul'  => self::GRUP_JUDUL[$m[1]] . ' ' . $n . ($n > 1 ? ' (opsional)' : ''),
            'nama'   => self::GRUP_JUDUL[$m[1]] . ' ' . $n,
        ];
    }

    public static function isOptional(string $key): bool
    {
        $g = self::group($key);

        return $g !== null && $g['n'] >= 2;
    }

    public static function label(string $key): ?string
    {
        $g = self::group($key);

        if ($g !== null && isset(self::GRUP_LABEL[$g['suffix']])) {
            return self::GRUP_LABEL[$g['suffix']];
        }

        $def = self::define($key);

        return $def['label'] ?? null;
    }

    public static function define(string $key, array $allKeys = []): ?array
    {
        $k = strtolower($key);

        if (self::dilewati($k)) {
            return null;
        }

        $def = self::eksplisit($k, $allKeys) ?? self::kelompok($k) ?? self::pola($k);

        if ($def === null) {
            return null;
        }

        $def['optional'] = self::isOptional($k);

        return $def;
    }

    private static function dilewati(string $k): bool
    {
        if (
            str_contains($k, 'tanggal')
            || str_contains($k, 'penandatangan')
            || str_contains($k, 'hrd')
            || str_contains($k, 'deskripsi_pelanggaran')
            || str_starts_with($k, 'nama_direktur')
        ) {
            return true;
        }

        if (preg_match('/^anggota_\d+_(nama|kontak)$/', $k)) {
            return true;
        }

        return in_array($k, [
            'nama_karyawan',
            'tingkat_sp',
            'paket_layanan',
            'nama_guru_pembimbing',
            'nama_dosen_pembimbing',
            'nama_atasan_langsung',
            'kontak_guru_pembimbing',
            'kontak_dosen',
            'kontak_atasan_langsung',
        ], true);
    }

    private static function eksplisit(string $k, array $allKeys): ?array
    {
        $rupiah = [
            'estimasi_biaya', 'nilai_kontrak', 'nilai_pekerjaan', 'total_nilai_pengadaan',
            'total_biaya_bulanan', 'besaran_gaji',
        ];

        if (in_array($k, $rupiah, true)) {
            return self::rupiah('rupiah');
        }

        return match ($k) {
            'biaya_berlangganan' => self::rupiah('angka'),

            'ringkasan_fitur' => self::area(
                7,
                "Contoh:\n- Login dan registrasi pengguna\n- Dashboard ringkasan data\n- Pengelolaan data produk\n- Laporan penjualan bulanan",
                'Tuliskan fitur-fitur utama aplikasi yang akan dikembangkan, misalnya login pengguna, dashboard, pengelolaan data, dan laporan.'
            ),
            'ringkasan_kebutuhan' => self::area(
                6,
                "Contoh:\n- Website company profile\n- Sistem pemesanan online\n- Dashboard admin",
                'Jelaskan kebutuhan utama klien, misalnya jenis sistem, fitur penting, dan target penggunanya.'
            ),
            'deskripsi_proyek' => self::area(
                4,
                'Contoh: Pengembangan sistem informasi inventaris berbasis web untuk mengelola stok barang di seluruh cabang.',
                'Jelaskan secara singkat gambaran proyek yang akan dikerjakan.'
            ),
            'ruang_lingkup_pekerjaan' => self::area(
                4,
                "Contoh:\n- Analisis kebutuhan\n- Perancangan UI/UX\n- Pengembangan dan pengujian sistem",
                'Uraikan pekerjaan yang harus dilaksanakan, misalnya analisis kebutuhan, perancangan, pengembangan, dan pengujian.'
            ),
            'ruang_lingkup_informasi' => self::area(
                4,
                'Contoh: Data pelanggan, dokumen proyek, kode sumber, dan informasi keuangan perusahaan.',
                'Sebutkan jenis informasi yang dirahasiakan, misalnya data klien, dokumen proyek, dan kode sumber.'
            ),
            'tujuan_kerjasama' => self::area(
                3,
                'Contoh: Kerja sama pengembangan sistem informasi untuk kebutuhan operasional kedua belah pihak.',
                'Jelaskan tujuan kerja sama yang melatarbelakangi perjanjian ini.'
            ),
            'alasan_maintenance' => self::area(
                3,
                'Contoh: Pembaruan keamanan sistem dan optimasi database.',
                'Jelaskan singkat alasan dilakukannya maintenance.'
            ),

            'platform_aplikasi' => self::centang(
                ['Android', 'iOS', 'Web'],
                'Tentukan platform yang menjadi target pengembangan aplikasi. Bisa pilih lebih dari satu, misalnya Android dan iOS.',
                'Pilih minimal satu platform.'
            ),
            'rincian_jadwal_wfo' => self::centang(
                ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                'Pilih hari kerja dari kantor (WFO) yang berlaku bagi karyawan. Bisa pilih lebih dari satu.',
                'Pilih minimal satu hari.',
                'Setiap hari '
            ),

            'jenis_layanan' => self::pilihanLainnya(
                [
                    'Pengembangan Website',
                    'Pengembangan Sistem Informasi',
                    'Pengembangan Aplikasi Mobile',
                    'Maintenance & Support',
                    'Lainnya',
                ],
                'Pilih jenis layanan yang ditawarkan. Pilih Lainnya bila tidak ada di daftar.'
            ),
            'status_kerja' => self::pilihan(
                ['Karyawan Tetap', 'Karyawan Kontrak', 'Magang', 'Freelance'],
                'Pilih status hubungan kerja karyawan.'
            ),
            'metode_pembayaran' => self::pilihan(
                ['Transfer Bank', 'Tunai', 'Pembayaran Bertahap (Termin)', 'Dibayar di Muka'],
                'Pilih metode pembayaran yang disepakati.'
            ),
            'bentuk_kerjasama' => self::pilihan(
                (in_array('program_keahlian', $allKeys, true) || in_array('nama_sekolah', $allKeys, true))
                    ? ['Praktik Kerja Lapangan (PKL)', 'Pelatihan Kerja', 'PKL dan Pelatihan Kerja']
                    : ['Kerja Praktik (KP)', 'Magang MBKM', 'Penelitian', 'KP dan Penelitian'],
                'Pilih bentuk kerja sama yang disepakati.'
            ),

            'estimasi_waktu_pengerjaan' => self::durasi(
                ['Hari', 'Minggu', 'Bulan'],
                'Bulan',
                'Isi lama pengerjaan proyek sejak kesepakatan, lalu pilih satuannya. Contoh: 3 Bulan atau 12 Minggu.'
            ),
            'masa_kontrak' => self::durasi(
                ['Bulan', 'Tahun'],
                'Bulan',
                'Isi lama masa kontrak kerja, lalu pilih satuannya. Contoh: 12 Bulan.'
            ),
            'durasi_kontrak' => self::durasi(
                ['Bulan', 'Tahun'],
                'Bulan',
                'Isi lama kontrak berlangganan, lalu pilih satuannya. Contoh: 12 Bulan.'
            ),
            'masa_berlaku_kerahasiaan' => self::durasi(
                ['Tahun', 'Bulan'],
                'Tahun',
                'Isi lama kewajiban menjaga kerahasiaan berlaku, lalu pilih satuannya. Contoh: 2 Tahun.'
            ),
            'durasi_maintenance' => self::durasi(
                ['Menit', 'Jam', 'Hari'],
                'Jam',
                'Isi perkiraan lama sistem tidak dapat diakses selama maintenance, lalu pilih satuannya. Contoh: 3 Jam.'
            ),

            'masa_berlaku_penawaran' => self::angka(
                'hari', ' hari', 'Contoh: 30',
                'Masukkan jumlah hari berlakunya penawaran sejak tanggal surat diterbitkan.',
                1, 365, 'Masa Berlaku Penawaran (Hari)'
            ),
            'batas_waktu_evaluasi' => self::angka(
                'hari kerja', null, 'Contoh: 7',
                'Masukkan jumlah hari kerja yang diberikan untuk memperbaiki sikap dan kinerja, dihitung sejak surat diterbitkan.',
                1, 365, 'Batas Waktu Evaluasi (Hari Kerja)'
            ),
            'durasi_bulan_magang' => self::angka(
                'bulan', null, 'Contoh: 3',
                'Masukkan lama magang dalam bulan (angka saja).',
                1, 36, 'Durasi Magang (Bulan)'
            ),
            'jumlah_mahasiswa' => self::angka(
                'orang', ' orang', 'Contoh: 10',
                'Masukkan jumlah mahasiswa yang terlibat dalam angka.'
            ),
            'jumlah_peserta' => self::angka(
                'orang', ' orang', 'Contoh: 10',
                'Masukkan jumlah peserta yang terlibat dalam angka.'
            ),

            'periode_bulan' => [
                'type'    => 'month',
                'helper'  => 'Pilih bulan dan tahun berlakunya jadwal WFO.',
                'render'  => 'bulan',
            ],
            'periode_magang' => [
                'type'   => 'date_range',
                'helper' => 'Isi tanggal mulai dan tanggal selesai periode magang.',
                'error'  => 'Isi tanggal mulai dan selesai. Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
                'render' => 'rentang',
            ],

            'nama_proyek'  => self::teks('Contoh: Sistem Informasi Inventaris'),
            'nama_sistem'  => self::teks('Contoh: Sistem Informasi Akademik'),
            'nama_pelatihan' => self::teks('Contoh: Workshop Dasar Keamanan Siber'),
            'nomor_kontrak' => self::teks(
                'Contoh: 012/SPK/INOVINDO/XI/2026',
                'Isi nomor kontrak atau SPK sesuai dokumen kesepakatan.'
            ),
            'tempat_pelaksanaan' => self::teks(
                'Contoh: Ruang Meeting Lantai 2',
                'Tulis lokasi atau media pelaksanaan, misalnya ruang meeting atau tautan Google Meet.'
            ),
            'lokasi_kantor' => self::teks(
                'Contoh: Kantor Pusat Bandung',
                'Isi unit atau lokasi penempatan kerja karyawan.'
            ),
            'bidang_keahlian_proyek' => self::teks('Contoh: Pengembangan Web (Laravel)'),
            'bidang_penempatan' => self::teks('Contoh: Pengembangan Web dan Aplikasi'),
            'program_studi', 'prodi_mahasiswa' => self::teks('Contoh: Teknik Informatika'),
            'program_keahlian' => self::teks('Contoh: Rekayasa Perangkat Lunak'),
            'nim_mahasiswa' => self::teks('Contoh: 2201234567', 'Isi NIM atau NRM mahasiswa.'),
            'nomor_induk_peserta' => self::teks('Contoh: 2201234567', 'Isi NIM (mahasiswa) atau NISN (siswa).'),
            'nidn_dosen' => self::teks('Contoh: 0712345678', 'Isi NIDN atau NIP dosen pembimbing.'),
            'nip_karyawan' => self::teks('Contoh: 19900101 2020 01 1 001', 'Isi NIP atau ID karyawan.'),

            default => null,
        };
    }

    private static function kelompok(string $k): ?array
    {
        $g = self::group($k);

        if ($g === null) {
            return null;
        }

        $p = $g['prefix'];

        return match ($g['suffix']) {
            'nama' => self::teks(match ($p) {
                'barang'  => 'Contoh: Laptop Core i5',
                'layanan' => 'Contoh: Cloud Hosting 4 GB',
                'peserta' => 'Contoh: Budi Santoso',
                default   => 'Contoh: Source code aplikasi',
            }),
            'jumlah' => self::angka(null, null, 'Contoh: 5', null, 1, 100000),
            'harga'  => self::rupiah('rupiah', 'Contoh: 1500000'),
            'biaya'  => self::rupiah('rupiah', 'Contoh: 500000'),
            'keterangan' => self::teks('Contoh: Sudah diuji dan berjalan normal'),
            'status' => self::pilihan(['Selesai', 'Diterima', 'Perlu Revisi'], null),
            'spesifikasi' => self::area(2, 'Contoh: 2 vCPU, 4 GB RAM, 80 GB SSD', null),
            'peran'  => self::teks('Contoh: Backend Developer'),
            'divisi' => self::teks('Contoh: Divisi IT'),
            'sesi'   => self::teks('Contoh: Sesi 1 (09.00 - 10.30)'),
            default  => null,
        };
    }

    private static function pola(string $k): ?array
    {
        if (preg_match(
            '/^nama_(klien|calon_klien|vendor|mitra|mahasiswa|siswa|peserta|project_manager|narasumber|mentor|mentor_lapangan|perwakilan_kampus|kepala_sekolah)$/',
            $k
        )) {
            return self::teks('Contoh: Budi Santoso');
        }

        if (preg_match('/^nama_(universitas|institusi|perguruan_tinggi)$/', $k)) {
            return self::teks('Contoh: Universitas PGRI Madiun');
        }

        if ($k === 'nama_sekolah') {
            return self::teks('Contoh: SMK Negeri 1 Bandung');
        }

        if (str_starts_with($k, 'nama_perusahaan_')) {
            return self::teks('Contoh: PT Maju Bersama');
        }

        if (str_starts_with($k, 'jabatan_')) {
            return self::teks('Contoh: Direktur Utama');
        }

        if (str_starts_with($k, 'posisi_')) {
            return self::teks('Contoh: Staff IT');
        }

        if (str_starts_with($k, 'divisi_')) {
            return self::teks('Contoh: Divisi IT');
        }

        if (str_starts_with($k, 'alamat_')) {
            return self::area(
                3,
                'Contoh: Jl. Merdeka No. 10, Kota Bandung, Jawa Barat',
                'Tulis alamat lengkap (jalan, nomor, kota, dan provinsi).'
            );
        }

        return null;
    }

    private static function teks(?string $placeholder, ?string $helper = null): array
    {
        return ['type' => 'text', 'placeholder' => $placeholder, 'helper' => $helper];
    }

    private static function area(int $rows, ?string $placeholder, ?string $helper): array
    {
        return [
            'type' => 'textarea', 'rows' => $rows,
            'placeholder' => $placeholder, 'helper' => $helper,
            'render' => 'nl2br',
        ];
    }

    private static function rupiah(string $render, string $placeholder = 'Contoh: 5000000'): array
    {
        return [
            'type' => 'rupiah', 'placeholder' => $placeholder,
            'helper' => self::HELPER_RUPIAH, 'render' => $render,
        ];
    }

    private static function angka(
        ?string $suffix,
        ?string $append,
        string $placeholder,
        ?string $helper,
        int $min = 1,
        int $max = 1000,
        ?string $label = null
    ): array {
        return [
            'type' => 'number', 'suffix' => $suffix, 'append' => $append,
            'placeholder' => $placeholder, 'helper' => $helper,
            'min' => $min, 'max' => $max, 'label' => $label,
            'render' => $append !== null ? 'tambah' : null,
        ];
    }

    private static function durasi(array $units, string $default, string $helper): array
    {
        return [
            'type' => 'duration', 'units' => $units, 'default' => $default,
            'helper' => $helper, 'error' => 'Isi lama dengan angka minimal 1, lalu pilih satuannya.',
        ];
    }

    private static function pilihan(array $options, ?string $helper): array
    {
        return ['type' => 'select', 'options' => $options, 'helper' => $helper];
    }

    private static function pilihanLainnya(array $options, ?string $helper): array
    {
        return [
            'type' => 'select_other', 'options' => $options, 'helper' => $helper,
            'error' => 'Pilih jenis layanan, atau tulis jenis lainnya.',
        ];
    }

    private static function centang(array $options, string $helper, string $error, ?string $prefix = null): array
    {
        return [
            'type' => 'checkbox', 'options' => $options, 'helper' => $helper,
            'error' => $error, 'render' => $prefix !== null ? 'awalan' : null, 'prefix' => $prefix,
        ];
    }

    public static function rules(array $keys): array
    {
        $keys = array_values(array_map('strval', $keys));
        $rules = [];

        foreach ($keys as $key) {
            $def = self::define($key, $keys);
            $rule = [self::isOptional($key) ? 'nullable' : 'required', 'string'];

            if ($def !== null) {
                $rule = array_merge($rule, self::aturanTipe($def, $key));
            }

            $rules['fields.' . $key] = $rule;
        }

        foreach ($keys as $key) {
            if (preg_match('/^tanggal_selesai_(.+)$/', $key, $m) && $m[1] !== 'layanan'
                && in_array('tanggal_mulai_' . $m[1], $keys, true)) {
                $rules['fields.' . $key] = [
                    'required', 'string', 'date', 'after_or_equal:fields.tanggal_mulai_' . $m[1],
                ];
            }

            if ($key === 'tanggal_target_selesai') {
                foreach (['tanggal_mulai_pekerjaan', 'tanggal_mulai_proyek'] as $mulai) {
                    if (in_array($mulai, $keys, true)) {
                        $rules['fields.' . $key] = [
                            'required', 'string', 'date', 'after_or_equal:fields.' . $mulai,
                        ];
                        break;
                    }
                }
            }
        }

        return $rules;
    }

    private static function aturanTipe(array $def, string $key): array
    {
        $label = self::namaUntukPesan($key);

        switch ($def['type']) {
            case 'rupiah':
                return ['integer', 'min:1', 'max:999999999999'];

            case 'number':
                return ['integer', 'min:' . $def['min'], 'max:' . $def['max']];

            case 'duration':
                $unit = implode('|', array_map(fn ($u) => preg_quote($u, '/'), $def['units']));

                return ['regex:/^[1-9][0-9]{0,3} (' . $unit . ')$/'];

            case 'month':
                return ['regex:/^[0-9]{4}-(0[1-9]|1[0-2])$/'];

            case 'textarea':
                return ['max:5000'];

            case 'select':
                $options = $def['options'];

                return [function ($attribute, $value, $fail) use ($options, $label) {
                    if (! in_array($value, $options, true)) {
                        $fail("Pilihan pada {$label} tidak valid.");
                    }
                }];

            case 'select_other':
                return ['max:150'];

            case 'checkbox':
                $options = $def['options'];

                return [function ($attribute, $value, $fail) use ($options, $label) {
                    foreach ($options as $opsi) {
                        if (preg_match('/\b' . preg_quote($opsi, '/') . '\b/i', (string) $value)) {
                            return;
                        }
                    }

                    $fail("Pilih minimal satu opsi pada {$label}.");
                }];

            case 'date_range':
                return [
                    'regex:/^[0-9]{4}-[0-9]{2}-[0-9]{2} s\.d\. [0-9]{4}-[0-9]{2}-[0-9]{2}$/',
                    function ($attribute, $value, $fail) use ($label) {
                        if (! preg_match('/^(\d{4}-\d{2}-\d{2}) s\.d\. (\d{4}-\d{2}-\d{2})$/', (string) $value, $m)) {
                            return;
                        }

                        if (! self::tanggalValid($m[1]) || ! self::tanggalValid($m[2]) || $m[2] < $m[1]) {
                            $fail("Tanggal selesai pada {$label} tidak boleh lebih awal dari tanggal mulai.");
                        }
                    },
                ];
        }

        return [];
    }

    public static function messages(array $keys): array
    {
        $keys = array_values(array_map('strval', $keys));
        $pesan = [];

        foreach ($keys as $key) {
            $label = self::namaUntukPesan($key);
            $p = 'fields.' . $key . '.';

            $pesan[$p . 'required'] = "{$label} wajib diisi.";
            $pesan[$p . 'string']   = "{$label} harus berupa teks.";
            $pesan[$p . 'integer']  = "{$label} harus berupa angka bulat tanpa titik atau koma.";
            $pesan[$p . 'min']      = "{$label} minimal :min.";
            $pesan[$p . 'max']      = "{$label} melebihi batas (maksimal :max).";
            $pesan[$p . 'date']     = "{$label} harus berupa tanggal yang valid.";
            $pesan[$p . 'after_or_equal'] = "{$label} tidak boleh lebih awal dari tanggal mulainya.";

            $def = self::define($key, $keys);
            $pesan[$p . 'regex'] = match ($def['type'] ?? null) {
                'duration'   => "Isi {$label} dengan angka minimal 1 dan pilih satuannya.",
                'month'      => "Pilih bulan dan tahun pada {$label}.",
                'date_range' => "Isi tanggal mulai dan tanggal selesai pada {$label}.",
                default      => "Format {$label} tidak valid.",
            };
        }

        return $pesan;
    }

    private static function namaUntukPesan(string $key): string
    {
        $g = self::group($key);
        $label = self::label($key) ?? ucwords(str_replace('_', ' ', $key));
        $label = str_replace(
            ['Nip', 'Wfo', 'Nim', 'Nidn', 'Nisn', 'Hrd', 'Spk'],
            ['NIP', 'WFO', 'NIM', 'NIDN', 'NISN', 'HRD', 'SPK'],
            $label
        );

        return $g !== null ? $g['nama'] . ' - ' . $label : $label;
    }

    public static function renderValue(string $key, string $value, array $allKeys = []): ?string
    {
        if (trim($value) === '' && self::isOptional($key)) {
            return '-';
        }

        $def = self::define($key, $allKeys);

        if ($def === null) {
            return null;
        }

        $bersih = trim($value);

        switch ($def['render'] ?? null) {
            case 'rupiah':
                return ctype_digit($bersih) ? 'Rp ' . number_format((int) $bersih, 0, ',', '.') : $value;

            case 'angka':
                return ctype_digit($bersih) ? number_format((int) $bersih, 0, ',', '.') : $value;

            case 'nl2br':
                return nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));

            case 'tambah':
                return ctype_digit($bersih) ? $bersih . $def['append'] : $value;

            case 'awalan':
                return $bersih === '' ? $value : $def['prefix'] . $bersih;

            case 'bulan':
                if (preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $bersih, $m)) {
                    return self::NAMA_BULAN[(int) $m[2]] . ' ' . $m[1];
                }

                return $value;

            case 'rentang':
                if (preg_match('/^(\d{4}-\d{2}-\d{2}) s\.d\. (\d{4}-\d{2}-\d{2})$/', $bersih, $m)) {
                    return self::tanggalIndonesia($m[1]) . ' s.d. ' . self::tanggalIndonesia($m[2]);
                }

                return $value;
        }

        return null;
    }

    private static function tanggalValid(string $iso): bool
    {
        return preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $iso, $m) === 1
            && checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
    }

    private static function tanggalIndonesia(string $iso): string
    {
        if (! self::tanggalValid($iso)) {
            return $iso;
        }

        [$tahun, $bulan, $hari] = array_map('intval', explode('-', $iso));

        return $hari . ' ' . self::NAMA_BULAN[$bulan] . ' ' . $tahun;
    }
}
