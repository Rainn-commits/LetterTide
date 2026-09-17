# LetterTide

**Sistem Manajemen Administrasi Berbasis Website pada PT Inovindo Digital Media**

LetterTide adalah aplikasi web internal untuk mengelola administrasi persuratan perusahaan, mulai dari pembuatan surat berbasis template, penomoran otomatis, tanda tangan digital, pengarsipan digital, hingga evaluasi masa simpan arsip menggunakan metode Sistem Pendukung Keputusan (TOPSIS + AHP).

Aplikasi ini dibangun sebagai output Kerja Praktik (KP) di PT Inovindo Digital Media.

---

## Daftar Isi

1. [Ringkasan Sistem](#ringkasan-sistem)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Struktur Hak Akses (Role)](#struktur-hak-akses-role)
4. [Fitur Aplikasi](#fitur-aplikasi)
5. [Alur Kerja Sistem](#alur-kerja-sistem)
6. [Struktur Database](#struktur-database)
7. [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
8. [Akun Bawaan (Seeder)](#akun-bawaan-seeder)
9. [Struktur Folder Penting](#struktur-folder-penting)
10. [Catatan Teknis](#catatan-teknis)

---

## Ringkasan Sistem

Sebelum adanya sistem ini, proses persuratan dilakukan secara manual menggunakan dokumen terpisah, sehingga penomoran surat rawan duplikat, arsip tersebar, dan tidak ada jejak siapa yang membuat atau menyetujui surat.

LetterTide menyelesaikan masalah tersebut dengan:

- **Template surat dinamis** — format surat dibuat sekali, lalu dipakai berulang hanya dengan mengisi data yang berubah.
- **Penomoran otomatis** — nomor surat digenerate sistem, tidak diketik manual.
- **Tanda tangan digital** — surat yang disetujui memperoleh QR Code yang dapat dipindai untuk verifikasi.
- **Arsip terpusat** — seluruh surat keluar otomatis tersimpan dalam bentuk PDF, ditambah arsip surat masuk dari pihak luar.
- **Audit trail** — setiap aksi penting tercatat pada Log Aktivitas Sistem.
- **Evaluasi retensi arsip** — sistem merekomendasikan status penyimpanan arsip (Musnah / Simpan Lokal / Permanen) berdasarkan perhitungan skor dengan metode TOPSIS + AHP.

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework Backend | Laravel 13 |
| Bahasa | PHP 8.3+ |
| Database | MySQL / MariaDB |
| Build Tool Frontend | Vite + Laravel Vite Plugin |
| Styling | Tailwind CSS v4 dan CSS kustom per fitur |
| Font | Playfair Display (judul) dan Inter (isi) |
| Generate PDF | barryvdh/laravel-dompdf |
| Generate QR Code | simplesoftwareio/simple-qrcode |
| Pengolahan Gambar | Ekstensi PHP GD (resize logo kop surat) |

---

## Struktur Hak Akses (Role)

Sistem memiliki tiga role bawaan. Role bersifat **dinamis** — Super Admin dapat membuat role baru dan mengatur hak akses per modul tanpa mengubah kode program.

| Modul / Fitur | Super Admin | Admin | User |
|---|:---:|:---:|:---:|
| Dashboard | Ya | Ya | Ya |
| Kelola Profil Sendiri | Ya | Ya | Ya |
| Buat Surat dan Riwayat Surat | Ya | Ya | Ya |
| Template Surat | Ya | Ya | Ya |
| Arsip Digital | Ya | Ya | Terbatas (surat sendiri) |
| Evaluasi Retensi Arsip | Ya | Ya | Terbatas (surat sendiri) |
| Pengaturan Kop Surat | Ya | Ya | Tidak |
| Kelola Pengguna | Ya | Ya | Tidak |
| Kelola Role dan Hak Akses | Ya | Tidak | Tidak |
| Log Aktivitas Sistem | Ya (penuh) | Ya (tanpa modul Role) | Tidak |
| Kelola Kriteria Retensi | Ya | Tidak | Tidak |

### Daftar Kode Hak Akses (Permission)

Hak akses disimpan pada tabel `permissions` dan dicek melalui middleware `permission:{kode}`.

| Kode | Keterangan |
|---|---|
| `role.manage` | Mengelola role dan hak akses |
| `user.manage` | Mengelola data pengguna |
| `kopsurat.manage` | Mengelola pengaturan kop surat |
| `template.manage` | Mengelola template surat |
| `surat.create` | Membuat surat baru |
| `surat.print` | Melihat detail, mencetak, dan mengunggah arsip surat |
| `arsip.manage` | Mengelola arsip digital |
| `log.view` | Melihat log aktivitas sistem |
| `retensi.manage` | Mengelola kriteria dan bobot evaluasi retensi |

### Aturan Pengaman Hierarki

Beberapa pembatasan diterapkan agar struktur wewenang tidak dapat disalahgunakan:

- Admin tidak dapat melihat, mengubah, maupun menonaktifkan akun Super Admin.
- Role "Super Admin" tidak dapat dipilih pada form Tambah Pengguna, dan terkunci (tidak dapat diubah) pada form Edit Pengguna.
- Super Admin tidak dapat menghapus hak akses `role.manage` dari role-nya sendiri, untuk mencegah kehilangan akses permanen.
- Role bawaan sistem (Super Admin, Admin, User) tidak dapat dihapus.
- Pengguna tidak dapat menonaktifkan atau menghapus akunnya sendiri.
- Penghapusan permanen pengguna hanya tersedia bila pengguna tersebut belum pernah tercatat sebagai pembuat atau penandatangan surat, demi menjaga integritas data arsip.
- Admin tidak dapat melihat entri Log Aktivitas dengan modul "Role", karena modul tersebut berada di luar wewenangnya.

---

## Fitur Aplikasi

### 1. Autentikasi

- Login dengan satu halaman untuk seluruh role, kemudian diarahkan otomatis ke dashboard sesuai role.
- Akun yang berstatus nonaktif ditolak masuk melalui middleware `active`.
- Registrasi mandiri **tidak tersedia**. Akun hanya dapat dibuat oleh Admin atau Super Admin melalui menu Kelola Pengguna.
- Lupa Password bersifat **permintaan**, bukan reset mandiri. Pengguna mengirim permintaan, lalu muncul sebagai notifikasi bagi Admin/Super Admin untuk direset secara manual.

### 2. Dashboard

Tampilan berbeda untuk tiap role, namun dengan gaya visual yang seragam:

- **Super Admin** — Total Surat, Surat Bulan Ini, Total Pengguna, Total Role, grafik Traffic Surat 6 bulan terakhir, dan panel Aktivitas Terbaru dengan pencarian.
- **Admin** — sama seperti Super Admin tanpa kartu Total Role.
- **User** — kartu Surat Saya, tombol pintas Buat Surat Baru, grafik surat pribadi, dan Riwayat Terakhir.

### 3. Kelola Profil

- Semua role dapat mengubah nama.
- Admin dan Super Admin dapat mengubah email serta mengganti password sendiri (dengan verifikasi password lama dan konfirmasi password baru).
- User tidak dapat mengubah email maupun password sendiri; perubahan dilakukan oleh Admin/Super Admin.

### 4. Kelola Template Surat

- CRUD template dengan **placeholder dinamis** menggunakan format `{{nama_field}}`.
- Placeholder terdeteksi otomatis saat mengetik dan ditampilkan sebagai daftar di bawah editor.
- Template mendukung **versioning**: setiap perubahan tersimpan sebagai versi baru sehingga surat lama tetap memakai format saat surat itu dibuat.
- Template dapat dinonaktifkan tanpa menghapus data.

Perilaku khusus pada field template:

| Pola nama field | Perilaku pada form Buat Surat |
|---|---|
| Mengandung kata `tanggal` | Ditampilkan sebagai pemilih tanggal (date picker) |
| Mengandung kata `penandatangan` atau `hrd` | Ditampilkan sebagai dropdown pilihan pengguna internal |
| `nama_karyawan` atau bertipe autocomplete | Ditampilkan sebagai input dengan saran nama pengguna |
| Lainnya | Input teks biasa |

Template juga mendukung **lampiran terpisah halaman** dengan menyisipkan penanda berikut di antara isi utama dan isi lampiran:

```html
<div style="page-break-before: always;"></div>
```

### 5. Buat Surat

Alur pembuatan surat:

1. Pilih template dari daftar (dilengkapi pencarian langsung).
2. Isi form: kop surat yang dipakai, penandatangan (approval sistem), lampiran, dan seluruh field dinamis dari template.
3. Sistem menyimpan surat, membuat nomor otomatis, dan langsung menghasilkan file PDF arsip.
4. Pengguna diarahkan ke halaman Preview Surat.

**Format penomoran otomatis:**

```
{nomor urut}/{kode template}/INOVINDO/{bulan romawi}/{tahun}
contoh: 007/PKSK/INOVINDO/IX/2026
```

Nomor urut dihitung per template per bulan, dan kode template diambil dari inisial nama template.

Menu Buat Surat memiliki dua tab:

- **Buat Surat Baru** — pemilihan template.
- **Riwayat Surat Saya** — daftar surat yang dibuat oleh pengguna yang sedang login, lengkap dengan pencarian, filter tanggal, filter template, serta aksi lihat, edit, cetak, dan hapus.

### 6. Preview, Cetak, dan PDF

Tiga keluaran ini dijaga agar **identik**: ukuran kertas A4 (210 x 297 mm) dengan margin 30 mm di keempat sisi.

- **Preview** — tampilan surat di dalam aplikasi.
- **Cetak** — halaman siap cetak yang langsung membuka dialog print browser.
- **PDF Arsip** — dibuat otomatis oleh sistem dan tersimpan ke storage.

PDF arsip di-generate ulang secara otomatis pada tiga kondisi: saat surat dibuat, saat isi surat diubah, dan saat surat ditandatangani secara digital.

### 7. Tanda Tangan Digital

- Surat yang disetujui memperoleh QR Code yang mengarah ke halaman detail surat, sehingga keaslian surat dapat diverifikasi dengan memindainya.
- QR Code digenerate secara lokal oleh server, bukan mengambil dari layanan luar, agar tetap muncul konsisten pada Preview, Cetak, maupun PDF.
- Surat yang belum disetujui menampilkan keterangan "(Belum Menyetujui / Ditandatangani)".
- Persetujuan hanya dapat dilakukan oleh pengguna yang ditunjuk sebagai penandatangan, atau oleh Admin/Super Admin.

### 8. Pengaturan Kop Surat

- Mendukung **versioning**: menyimpan perubahan akan membuat versi baru dan menonaktifkan versi sebelumnya, sehingga surat lama tetap memakai kop surat yang berlaku saat itu.
- Tersedia **Live Preview** saat mengisi form, serta **halaman Preview** untuk melihat kembali kop surat versi aktif maupun versi lama pada simulasi surat utuh.
- Logo yang diunggah **otomatis diseragamkan** ke kanvas 400 x 150 piksel dengan latar putih, sehingga proporsi kop surat konsisten berapapun ukuran file aslinya.
- Riwayat versi ditampilkan dalam tabel beserta aksi preview.

### 9. Arsip Digital

Terbagi menjadi dua folder:

- **Surat Keluar** — seluruh surat yang dibuat melalui sistem, otomatis masuk ke sini beserta file PDF-nya.
- **Surat Masuk** — arsip dokumen dari pihak luar, diinput manual (nomor surat pengirim, instansi asal, perihal, tanggal, dan file).

Fitur pendukung: pencarian, filter tanggal, filter template, filter status file, dan filter cakupan surat (semua surat atau surat buatan sendiri, khusus Admin/Super Admin).

Aksi yang tersedia: lihat detail, cetak, ACC dan tanda tangan digital, unggah/ganti file arsip, serta hapus surat.

User hanya melihat surat miliknya sendiri pada halaman ini.

### 10. Kelola Pengguna dan Role

Kedua modul digabung dalam satu menu. Super Admin melihat dua tab (Pengguna dan Role dan Hak Akses), sedangkan Admin hanya melihat halaman Pengguna.

**Kelola Pengguna:**

- Tambah, edit, dan atur role pengguna.
- Reset password pengguna lain tanpa perlu mengetahui password lama (dikosongkan bila tidak ingin diubah).
- Nonaktifkan pengguna dan aktifkan kembali.
- Hapus permanen, tersedia hanya bila pengguna belum pernah tercatat pada surat manapun.

**Kelola Role dan Hak Akses:**

- Membuat role baru dan mengatur hak akses per modul melalui checkbox.
- Menampilkan jumlah pengguna yang memakai tiap role.
- Role yang masih dipakai pengguna tidak dapat dihapus.

### 11. Log Aktivitas Sistem

Audit trail yang mencatat aksi penting beserta pelaku dan waktunya. Modul yang tercatat meliputi Surat, Surat Masuk, Template Surat, Kop Surat, Pengguna, dan Role.

Log bersifat **hanya-baca** — tidak ada fitur edit maupun hapus, sehingga jejak aktivitas tetap dapat dipercaya sebagai bukti. Admin dapat mengakses log, kecuali entri dengan modul "Role".

Data sensitif seperti password tidak pernah dicatat pada log.

### 12. Evaluasi Retensi Arsip (SPK)

Fitur pendukung keputusan untuk menentukan masa simpan arsip menggunakan metode **TOPSIS + AHP**.

**Kriteria penilaian:**

| Kode | Kriteria | Tipe | Bobot Bawaan |
|---|---|---|---|
| K1 | Nilai Guna Dokumen | Benefit | 0.4 |
| K2 | Sifat / Kerahasiaan Surat | Benefit | 0.3 |
| K3 | Masa Simpan / Umur Surat | Cost | 0.2 |
| K4 | Frekuensi Akses / Download | Benefit | 0.1 |

K3 bertipe *cost*, artinya semakin lama sebuah arsip tersimpan, semakin rendah skornya dan semakin condong ke rekomendasi Musnah. K4 dihitung otomatis dari jumlah akses surat, sedangkan K1 dan K2 diisi manual oleh pengguna.

Bobot tiap kriteria dapat diubah melalui menu Kelola Kriteria Retensi, dengan total bobot ideal 1.0.

**Klasifikasi hasil skor:**

| Rentang Skor | Rekomendasi |
|---|---|
| >= 0.70 | Permanen |
| 0.40 - 0.69 | Simpan Lokal |
| < 0.40 | Musnah |

**Alur evaluasi:**

1. Pengguna mengisi Nilai Guna dan Kerahasiaan pada surat, lalu menyimpan.
2. Sistem langsung menghitung ulang skor seluruh arsip yang telah dinilai. Perhitungan bersifat relatif antar arsip, sehingga penambahan data baru memengaruhi skor arsip lain.
3. Hasil berstatus **Menunggu Validasi** hingga disetujui Admin/Super Admin.
4. Admin/Super Admin melakukan validasi, dan status berubah menjadi **Tervalidasi**.

**Pembatasan tampilan:** User tidak melihat kolom skor maupun status rekomendasi (Musnah / Simpan Lokal / Permanen). User hanya melihat status prosesnya: Belum Dievaluasi, Menunggu Validasi, atau Tervalidasi. Keputusan retensi merupakan wewenang administratif.

Halaman ini dilengkapi pencarian, filter tanggal, filter template, filter status evaluasi, dan pengurutan (tanggal, jumlah akses, serta skor untuk Admin/Super Admin).

---

## Alur Kerja Sistem

### Alur Surat Keluar

```
Admin/Super Admin menyiapkan Kop Surat dan Template Surat
                    |
                    v
Pengguna memilih template dan mengisi data surat
                    |
                    v
Sistem membuat nomor surat otomatis + menyimpan isian surat
                    |
                    v
Sistem menghasilkan PDF dan menyimpannya ke Arsip Digital
                    |
                    v
Penandatangan (atau Admin/Super Admin) melakukan ACC & TTD Digital
                    |
                    v
PDF diperbarui dengan QR Code verifikasi; surat berstatus Disetujui
                    |
                    v
Surat dievaluasi masa simpannya pada menu Evaluasi Retensi Arsip
```

### Alur Surat Masuk

```
Dokumen diterima dari pihak luar
                    |
                    v
Admin/Super Admin membuka Arsip Digital > folder Surat Masuk
                    |
                    v
Input nomor surat pengirim, instansi asal, perihal, tanggal, dan unggah file
                    |
                    v
Tersimpan ke arsip dan dapat dicari sewaktu-waktu
```

---

## Struktur Database

| Tabel | Keterangan |
|---|---|
| `users` | Data pengguna, memiliki `role_id` dan status `is_aktif` |
| `roles` | Daftar role (Super Admin, Admin, User, dan role tambahan) |
| `permissions` | Daftar hak akses per modul |
| `role_permissions` | Tabel pivot penghubung role dengan hak akses |
| `template_surat` | Template surat beserta nomor versi dan status aktif |
| `surat` | Data surat keluar maupun surat masuk |
| `isi_surat` | Isian field surat dengan pola EAV (`nama_field` dan `nilai_field`) |
| `pengaturan_kop_surat` | Kop surat beserta versi dan status aktif |
| `log_aktivitas` | Catatan audit trail aktivitas sistem |
| `retensi_kriteria` | Kriteria K1 sampai K4 beserta bobot dan tipe |
| `evaluasi_retensi` | Hasil penilaian, skor, rekomendasi, dan status validasi |
| `notifications` | Notifikasi bawaan Laravel, dipakai untuk permintaan reset password |

### Relasi Utama

```
roles 1 ---- N users 1 ---- N surat 1 ---- N isi_surat
  |                           |
  |                           +---- 1 evaluasi_retensi
  N                           |
  |                           +---- N template_surat (template_id)
role_permissions              |
  |                           +---- N pengaturan_kop_surat (kop_surat_id)
  N
permissions
```

Catatan: kolom `template_id` dan `kop_surat_id` pada tabel `surat` bersifat *nullable*, karena surat masuk dari pihak luar tidak memakai template maupun kop surat perusahaan.

---

## Instalasi dan Konfigurasi

### Prasyarat

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan NPM
- MySQL / MariaDB
- **Ekstensi PHP GD dalam keadaan aktif** (wajib, dipakai untuk resize logo kop surat dan pembuatan PDF)

Cara memastikan GD aktif:

```bash
php -m
```

Bila `gd` tidak muncul pada daftar, buka file `php.ini` (lokasinya dapat dilihat dengan `php --ini`), lalu hapus tanda titik koma pada baris berikut:

```ini
extension=gd
```

Simpan, kemudian jalankan ulang server.

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/Rainn-commits/LetterFlow.git
cd LetterFlow

# 2. Install dependency backend
composer install

# 3. Install dependency frontend
npm install

# 4. Siapkan file environment
cp .env.example .env
php artisan key:generate

# 5. Buat symbolic link storage (wajib, agar logo dan PDF dapat diakses)
php artisan storage:link
```

### Konfigurasi `.env`

```env
APP_NAME=LetterTide
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lettertide_db
DB_USERNAME=root
DB_PASSWORD=
```

### Konfigurasi Zona Waktu

Pastikan `config/app.php` memakai zona waktu Indonesia agar tanggal dan jam pada Log Aktivitas serta surat sesuai WIB:

```php
'timezone' => 'Asia/Jakarta',
```

### Migrasi dan Data Awal

```bash
php artisan migrate --seed
```

### Menjalankan Aplikasi

```bash
# Terminal 1 - server Laravel
php artisan serve

# Terminal 2 - build asset frontend (biarkan tetap berjalan)
npm run dev
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`.

Untuk kebutuhan produksi, build asset terlebih dahulu:

```bash
npm run build
```

---

## Akun Bawaan (Seeder)

Seeder membuat lima akun awal untuk keperluan pengujian:

| Email | Role |
|---|---|
| superadmin@gmail.com | Super Admin |
| admin@gmail.com | Admin |
| user1@gmail.com | User |
| user2@gmail.com | User |
| user3@gmail.com | User |

Password masing-masing akun dapat dilihat dan diubah pada `database/seeders/DatabaseSeeder.php`.

**Penting:** seeder memakai `updateOrCreate`, sehingga menjalankan `php artisan db:seed` kembali akan **menimpa** data akun tersebut dengan nilai yang tertulis pada file seeder, termasuk password yang mungkin sudah diubah melalui aplikasi. Hindari menjalankan seeder pada database yang sudah berisi data aktif.

Segera ganti seluruh password bawaan sebelum aplikasi digunakan secara nyata.

---

## Struktur Folder Penting

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                     Login dan permintaan reset password
│   │   ├── DashboardController.php   Dashboard tiap role
│   │   ├── ProfileController.php     Kelola profil dan ganti password sendiri
│   │   ├── SuratController.php       Buat surat, preview, cetak, PDF, TTD digital
│   │   ├── SuratMasukController.php  Arsip surat masuk
│   │   ├── ArsipController.php       Arsip digital dan filternya
│   │   ├── TemplateSuratController.php
│   │   ├── KopSuratController.php    Termasuk resize logo otomatis
│   │   ├── PenggunaController.php
│   │   ├── RoleController.php
│   │   ├── LogAktivitasController.php
│   │   ├── EvaluasiRetensiController.php
│   │   └── RetensiKriteriaController.php
│   └── Middleware/                   Middleware active dan permission
├── Models/
│   ├── User.php  Role.php  Permission.php
│   ├── Surat.php  IsiSurat.php  TemplateSurat.php
│   ├── PengaturanKopSurat.php  LogAktivitas.php
│   └── RetensiKriteria.php  EvaluasiRetensi.php
└── Services/
    ├── PlaceholderParser.php         Parsing dan render placeholder template
    └── RetensiScoringService.php     Perhitungan skor retensi arsip

resources/
├── css/                              Satu file CSS per fitur
│   ├── variables.css                 Token warna, radius, dan font
│   ├── app-shell.css                 Sidebar, topbar, dan modal konfirmasi
│   ├── surat-*.css                   CSS terpisah per halaman surat
│   └── ...
├── js/
│   ├── confirm-modal.js              Modal konfirmasi bertema
│   ├── surat-wizard.js               Pencarian template dan autocomplete
│   ├── template-editor.js            Deteksi placeholder otomatis
│   └── dashboard-interactions.js
└── views/
    ├── layouts/app.blade.php         Kerangka utama aplikasi
    ├── partials/                     Sidebar dan topbar
    ├── surat/                        pilih-template, form, preview, cetak, pdf,
    │                                 edit, arsip, riwayat
    ├── kop-surat/  template-surat/  pengguna/  role/
    └── evaluasi-retensi/  retensi-kriteria/  log-aktivitas/
```

---

## Catatan Teknis

### Modal Konfirmasi

Seluruh aksi berisiko (hapus, nonaktifkan, tanda tangan, validasi) memakai modal konfirmasi bertema, bukan dialog bawaan browser. Cara memakainya cukup menambahkan atribut pada elemen form:

```html
<form method="POST" action="..."
      data-confirm="Pesan konfirmasi yang ingin ditampilkan"
      data-confirm-variant="danger"
      data-confirm-label="Ya, Hapus">
```

| Atribut | Fungsi |
|---|---|
| `data-confirm` | Pesan yang ditampilkan (wajib, sekaligus pemicu modal) |
| `data-confirm-variant` | Isi `danger` untuk aksi destruktif, kosongkan untuk aksi biasa |
| `data-confirm-label` | Teks pada tombol konfirmasi |

### Tampilan Responsif

- Di atas 992 piksel, sidebar tampil menetap di sisi kiri.
- Di bawah 992 piksel, sidebar berubah menjadi panel geser yang dibuka melalui tombol menu pada topbar, dan tertutup otomatis saat pengguna mengklik area di luarnya.

### Konsistensi Tipografi

Seluruh tabel dan form memakai ukuran seragam agar tampilan konsisten antar fitur:

| Elemen | Ukuran |
|---|---|
| Judul kolom tabel | 13 px, tebal, huruf kapital |
| Isi baris tabel | 13.5 px |
| Jarak dalam sel tabel | 14 px atas-bawah, 18 px kiri-kanan |
| Label form | 13 px, tebal |
| Input dan dropdown | 14 px |

Judul halaman dan subjudul dikecualikan dari aturan ini dan tetap memakai font Playfair Display.

### Regenerasi PDF Arsip Secara Massal

Bila dilakukan perubahan pada tata letak PDF, file PDF lama tidak ikut berubah karena sudah tersimpan sebagai file fisik. Untuk memperbarui seluruhnya, jalankan proses regenerasi melalui `php artisan tinker` dengan memanggil ulang alur pembuatan PDF pada tiap surat berjenis Keluar.

### Keterbatasan yang Diketahui

- Template surat belum mendukung baris tabel dinamis. Lampiran berupa daftar memakai jumlah baris tetap dengan field terpisah, misalnya `item_1_nama`, `item_2_nama`, dan seterusnya.
- Reset password bersifat manual oleh Admin/Super Admin, belum melalui email otomatis.
- Rekomendasi retensi arsip berhenti pada tahap rekomendasi dan validasi; sistem belum melakukan eksekusi pemusnahan arsip secara otomatis.

---

## Informasi Project

| Keterangan | Detail |
|---|---|
| Nama Aplikasi | LetterTide |
| Instansi | PT Inovindo Digital Media, Bandung |
| Jenis | Kerja Praktik (KP) |
| Institusi | Universitas PGRI Madiun |
| Program Studi | Teknik Informatika |

