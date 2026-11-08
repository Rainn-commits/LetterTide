<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tabel = [
        'template_surat',
        'pengaturan_kop_surat',
        'surat',
        'log_aktivitas',
        'retensi_kriteria',
    ];

    public function up(): void
    {
        foreach ($this->tabel as $nama) {
            if (!Schema::hasColumn($nama, 'unit_kerja_id')) {
                Schema::table($nama, function (Blueprint $table) {
                    $table->foreignId('unit_kerja_id')
                        ->nullable()
                        ->constrained('unit_kerja')
                        ->nullOnDelete();
                });
            }
        }

        $unit = DB::table('unit_kerja')->pluck('id', 'kode');
        $idPusat = $unit['PUSAT'];

        DB::table('surat')->whereNull('unit_kerja_id')->update([
            'unit_kerja_id' => DB::raw('(SELECT u.unit_kerja_id FROM users u WHERE u.id = surat.user_id)'),
        ]);
        DB::table('log_aktivitas')->whereNull('unit_kerja_id')->update([
            'unit_kerja_id' => DB::raw('(SELECT u.unit_kerja_id FROM users u WHERE u.id = log_aktivitas.user_id)'),
        ]);

        DB::table('pengaturan_kop_surat')->whereNull('unit_kerja_id')->update(['unit_kerja_id' => $idPusat]);

        $pembagian = [
            'ACADEMY' => ['perguruan tinggi', 'sekolah', 'pkl', 'mahasiswa', 'rekomendasi kinerja', 'undangan pelatihan', 'pelatihan'],
            'WEB'     => ['penawaran', 'kerja sama proyek', 'perintah kerja', 'spk', 'penugasan tim', 'berita acara', 'pemberitahuan maintenance', 'berlangganan', 'cloud', 'hosting'],
            'PUSAT'   => ['keterangan karyawan', 'kontrak kerja', 'wfo', 'perjanjian pengadaan', 'pengadaan', 'nda', 'kerahasiaan'],
        ];

        $templates = DB::table('template_surat')->whereNull('unit_kerja_id')->select('id', 'nama_template')->get();

        foreach ($templates as $t) {
            $nama = mb_strtolower($t->nama_template);
            foreach ($pembagian as $kodeUnit => $kataKunci) {
                foreach ($kataKunci as $kata) {
                    if (preg_match('/(?<![a-z])' . preg_quote($kata, '/') . '(?![a-z])/u', $nama)) {
                        DB::table('template_surat')->where('id', $t->id)->update(['unit_kerja_id' => $unit[$kodeUnit]]);
                        continue 3;
                    }
                }
            }
        }

        DB::table('retensi_kriteria')->whereNull('unit_kerja_id')->update(['unit_kerja_id' => $idPusat]);

        $dasar = DB::table('retensi_kriteria')->where('unit_kerja_id', $idPusat)->get();

        foreach (['WEB', 'ACADEMY'] as $kodeUnit) {
            if (DB::table('retensi_kriteria')->where('unit_kerja_id', $unit[$kodeUnit])->exists()) {
                continue;
            }

            foreach ($dasar as $k) {
                DB::table('retensi_kriteria')->insert([
                    'kode'          => $k->kode,
                    'nama_kriteria' => $k->nama_kriteria,
                    'bobot'         => $k->bobot,
                    'tipe'          => $k->tipe,
                    'unit_kerja_id' => $unit[$kodeUnit],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tabel as $nama) {
            if (Schema::hasColumn($nama, 'unit_kerja_id')) {
                Schema::table($nama, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('unit_kerja_id');
                });
            }
        }
    }
};
