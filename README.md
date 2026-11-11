# LetterTide

**Sistem Manajemen Administrasi Berbasis Website pada PT Inovindo Digital Media**

LetterTide adalah aplikasi web internal untuk mengelola administrasi persuratan perusahaan, mulai dari pembuatan surat berbasis template, penomoran otomatis, tanda tangan digital, pengarsipan digital, hingga evaluasi masa simpan arsip menggunakan metode Sistem Pendukung Keputusan (TOPSIS + AHP).

Data dan akun dipisahkan per **Unit Kerja** (Kantor Pusat, Inovindo Web, dan Inovindo Academy), sehingga setiap Admin dan User hanya bekerja dengan template, kop surat, surat, dan arsip milik unitnya sendiri.

Aplikasi ini dibangun sebagai output Kerja Praktik (KP) di PT Inovindo Digital Media.

---

## Daftar Isi

1. [Ringkasan Sistem](#ringkasan-sistem)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Unit Kerja dan Pemisahan Data](#unit-kerja-dan-pemisahan-data)
4. [Struktur Hak Akses (Role)](#struktur-hak-akses-role)
5. [Fitur Aplikasi](#fitur-aplikasi)
6. [Alur Kerja Sistem](#alur-kerja-sistem)
7. [Struktur Database](#struktur-database)
8. [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
9. [Akun Bawaan (Seeder)](#akun-bawaan-seeder)
10. [Struktur Folder Penting](#struktur-folder-penting)
11. [Catatan Teknis](#catatan-teknis)

---

## Ringkasan Sistem

Sebelum adanya sistem ini, proses persuratan dilakukan secara manual menggunakan dokumen terpisah, sehingga penomoran surat rawan duplikat, arsip tersebar, dan tidak ada jejak siapa yang membuat atau menyetujui surat.

LetterTide menyelesaikan masalah tersebut dengan:

- **Template surat dinamis** - format surat dibuat sekali, lalu dipakai berulang hanya dengan mengisi data yang berubah. Kolom isian dibuat otomatis dan disesuaikan jenisnya (tanggal, nominal rupiah, pilihan, dan sebagainya) agar mudah dipahami pengguna.
- **Penomoran otomatis** - nomor surat digenerate sistem, tidak diketik manual.
- **Tanda tangan digital** - surat yang disetujui memperoleh QR Code yang dapat dipindai untuk verifikasi.
- **Arsip terpusat** - seluruh surat keluar otomatis tersimpan dalam bentuk PDF, ditambah arsip surat masuk dari pihak luar.
- **Pemisahan per unit kerja** - template, kop surat, surat, arsip, log, dan kriteria retensi dipisahkan per unit kerja.
- **Audit trail** - setiap aksi penting tercatat pada Log Aktivitas Sistem.
- **Evaluasi retensi arsip** - sistem merekomendasikan status penyimpanan arsip (Musnah/Simpan Lokal/Permanen) berdasarkan perhitungan skor dengan metode TOPSIS + AHP.

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework Backend | Laravel 13 |
| Bahasa | PHP 8.3+ |
| Database | MySQL/MariaDB |
| Build Tool Frontend | Vite + Laravel Vite Plugin |
| Styling | Tailwind CSS v4 dan CSS kustom per fitur |
| Font | Playfair Display (judul) dan Inter (isi) |
| Generate PDF | barryvdh/laravel-dompdf |
| Generate QR Code | simplesoftwareio/simple-qrcode |
| Pengolahan Gambar | Ekstensi PHP GD (resize logo kop surat) |

---

## Unit Kerja dan Pemisahan Data

Sistem memiliki tiga unit kerja bawaan:

| Kode | Unit Kerja |
|---|---|
| `PUSAT` | Kantor Pusat |
| `WEB` | Inovindo Web |
| `ACADEMY` | Inovindo Academy |

Setiap akun Admin dan User terdaftar pada tepat satu unit kerja. Super Admin **tidak terikat unit** karena bertugas mengelola seluruh unit.

### Apa yang dipisahkan per unit

| Data | Perilaku |
|---|---|
| Template Surat | Setiap template dimiliki satu unit. Menu Buat Surat hanya menampilkan template milik unit pengguna. |
| Kop Surat | Setiap kop dimiliki satu unit. Admin dan User hanya melihat kop unitnya. |
| Surat dan Arsip | Surat otomatis tercatat atas unit pembuatnya, dan hanya terlihat oleh unit tersebut. |
| Log Aktivitas | Admin hanya melihat aktivitas unitnya. Super Admin melihat semuanya. |
| Kriteria Retensi | Setiap unit punya bobot K1 sampai K4 sendiri, dan skor dihitung hanya di antara arsip dalam unit yang sama. |
| Daftar Pengguna | Admin hanya melihat akun pada unitnya. Daftar penandatangan dan pencarian nama juga dibatasi ke unit sendiri. |

Mengakses data milik unit lain (misalnya dengan menebak alamat URL) menghasilkan halaman **404**.

Pemisahan ini dijalankan otomatis oleh trait `BerdasarkanUnitKerja` yang dipasang pada model Template Surat, Kop Surat, Surat, Log Aktivitas, dan Kriteria Retensi. Trait ini juga mengisi unit kerja secara otomatis ketika Admin/User membuat data baru.

### Pembagian awal 20 template surat

| Unit Kerja | Template |
|---|---|
| Kantor Pusat (5) | Surat Keterangan Karyawan Aktif, Kontrak Kerja, Jadwal WFO, Perjanjian Pengadaan, Perjanjian Kerahasiaan (NDA) |
| Inovindo Web (9) | Penawaran Aplikasi Mobile, Penawaran Sistem/Website, Penawaran Maintenance dan Support, Perjanjian Kerja Sama Proyek, Surat Perintah Kerja (SPK), Penugasan Tim Proyek, Berita Acara Serah Terima, Pemberitahuan Maintenance, Perjanjian Berlangganan Cloud/Hosting |
| Inovindo Academy (6) | Kerja Sama Perguruan Tinggi, Kerja Sama Sekolah, Surat Pengantar PKL Siswa, Surat Pengantar Mahasiswa, Rekomendasi Kinerja, Undangan Pelatihan |

Pembagian dilakukan otomatis oleh migrasi berdasarkan kata kunci pada nama template. Template yang namanya tidak cocok dengan kata kunci apa pun muncul pada grup **"Belum ditentukan unitnya"** di halaman Template Surat milik Super Admin, lalu dapat dipindahkan melalui menu Edit.

### Aturan "lintas unit"

Sebuah role dianggap **lintas unit** (melihat semua unit) bila memiliki hak akses `role.manage`. Secara bawaan hanya Super Admin yang memilikinya. Karena mengikuti hak akses, aturan ini tetap berlaku untuk role buatan Super Admin sendiri.

---

## Struktur Hak Akses (Role)

Sistem memiliki tiga role bawaan. Role bersifat **dinamis** - Super Admin dapat membuat role baru dan mengatur hak akses per fitur tanpa mengubah kode program. Fitur yang diberikan akan muncul di sidebar role tersebut, dan ketika hak aksesnya dicabut, menu itu otomatis hilang dari sidebar dan tidak dapat diakses lagi.

| Fitur | Super Admin | Admin (per unit) | User (per unit) |
|---|:---:|:---:|:---:|
| Dashboard | Ya (semua unit) | Ya (unit sendiri) | Ya (data pribadi) |
| Kelola Profil | Ya | Ya | Ya |
| Template Surat | Ya (semua unit, dikelompokkan per unit) | Tidak | Tidak |
| Pengaturan Kop Surat | Buat, ubah, dan hapus (semua unit) | Hanya ubah dan hapus kop unit sendiri | Tidak |
| Kelola Pengguna dan Role | Ya (semua unit) | Hanya ubah nama dan nonaktifkan/aktifkan akun di unit sendiri | Tidak |
| Buat Surat | Tidak | Ya | Ya |
| Arsip Digital | Tidak | Ya (semua surat di unit) | Terbatas (surat sendiri) |
| Retensi Arsip | Tidak | Evaluasi, validasi, dan atur bobot kriteria lewat perbandingan berpasangan AHP | Hanya mengisi penilaian |
| Log Aktivitas Sistem | Ya (semua unit) | Ya (unit sendiri) | Tidak |

Super Admin berperan sebagai pengelola sistem, sehingga tidak membuat surat, tidak mengelola arsip, dan tidak mengikuti proses retensi. Hal-hal yang menyangkut password dan pembuatan akun hanya dapat dilakukan Super Admin.

### Daftar Kode Hak Akses (Permission)

Hak akses disimpan pada tabel `permissions` dan dicek melalui middleware `permission:{kode}`. Middleware menerima lebih dari satu kode, misalnya `permission:kopsurat.manage,kopsurat.edit`, yang berarti cukup memiliki salah satunya.

| Kode | Keterangan | Bawaan untuk |
|---|---|---|
| `surat.create` | Membuat surat, melihat riwayat, detail, dan cetak surat | Admin, User |
| `arsip.manage` | Mengakses arsip digital | Admin, User |
| `template.manage` | Mengelola template surat | Super Admin |
| `kopsurat.manage` | Membuat kop surat baru (dan mengelola semua kop) | Super Admin |
| `kopsurat.edit` | Mengubah dan menghapus kop surat yang sudah ada pada unit sendiri | Admin |
| `role.manage` | Mengelola role, hak akses, akun, dan melihat semua unit (lintas unit) | Super Admin |
| `pengguna.unit` | Mengelola akun pada unit sendiri (ubah nama, nonaktifkan, aktifkan) | Admin |
| `retensi.evaluate` | Mengakses fitur retensi arsip | Admin, User |
| `retensi.manage` | Mengelola kriteria retensi: matriks perbandingan AHP dan detail perhitungan TOPSIS, untuk unit sendiri | Admin |
| `log.view` | Melihat log aktivitas sistem | Super Admin, Admin |

### Aturan Pengaman Hierarki

Beberapa pembatasan diterapkan agar struktur wewenang tidak dapat disalahgunakan:

- Admin hanya dapat mengelola akun di unit kerjanya sendiri, dan tidak dapat melihat, mengubah, atau menonaktifkan akun Super Admin maupun Admin lain.
- Admin tidak dapat menambah akun, mengubah email, role, unit, atau password, dan tidak dapat menghapus akun secara permanen. Semuanya hanya dapat dilakukan Super Admin.
- Role "Super Admin" tidak dapat dipilih pada form Tambah Pengguna, dan terkunci (tidak dapat diubah) pada form Edit Pengguna.
- Super Admin wajib memilih unit kerja saat menambah atau mengubah akun (kecuali akun Super Admin).
- Super Admin tidak dapat menghapus hak akses `role.manage` dari role-nya sendiri, untuk mencegah kehilangan akses permanen.
- Role bawaan sistem (Super Admin, Admin, User) tidak dapat dihapus, dan role yang masih dipakai pengguna tidak dapat dihapus.
- Pengguna tidak dapat menonaktifkan atau menghapus akunnya sendiri.
- Penghapusan permanen pengguna hanya tersedia bila pengguna tersebut belum pernah tercatat sebagai pembuat atau penandatangan surat, demi menjaga integritas data arsip.
- Admin tidak melihat entri Log Aktivitas dengan modul "Role" dan "Pengguna" milik Super Admin.

---

## Fitur Aplikasi

### 1. Autentikasi

- Login dengan satu halaman untuk seluruh role, kemudian diarahkan otomatis ke dashboard sesuai role. Kolom password dilengkapi tombol tampilkan/sembunyikan.
- Akun yang berstatus nonaktif ditolak masuk melalui middleware `active`.
- Registrasi mandiri **tidak tersedia**. Akun hanya dapat dibuat oleh Super Admin melalui menu Kelola Pengguna.
- Lupa Password bersifat **permintaan**, bukan reset mandiri. Pengguna mengirim permintaan, lalu muncul sebagai notifikasi bagi Super Admin untuk direset secara manual.

### 2. Dashboard

Tampilan berbeda untuk tiap role, namun dengan gaya visual yang seragam:

- **Super Admin** - Total Surat, Surat Bulan Ini, Total Pengguna, Total Role (seluruh unit), grafik Traffic Surat 6 bulan terakhir, dan panel Aktivitas Terbaru.
- **Admin** - Total Surat, Surat Bulan Ini, dan Total Pengguna **pada unitnya**, grafik Traffic Surat 6 bulan terakhir, dan Aktivitas Terbaru.
- **User** - kartu Surat Saya, tombol pintas Buat Surat Baru, grafik surat pribadi, dan Riwayat Terakhir.

### 3. Kelola Profil

- Semua role dapat mengubah nama.
- Admin dan Super Admin dapat mengubah email serta mengganti password sendiri (dengan verifikasi password lama dan konfirmasi password baru).
- User tidak dapat mengubah email maupun password sendiri; perubahan dilakukan oleh Super Admin.

### 4. Kelola Template Surat (Super Admin)

- CRUD template dengan **placeholder dinamis** menggunakan format `{{nama_field}}`.
- Placeholder terdeteksi otomatis saat mengetik dan ditampilkan sebagai daftar di bawah editor.
- Setiap template dimiliki satu **unit kerja** yang dipilih saat membuat atau mengedit template. Memindahkan unit berlaku untuk semua versi template tersebut.
- Halaman daftar template dikelompokkan per unit kerja, dengan grup "Belum ditentukan unitnya" di bagian bawah bila ada.
- Template mendukung **versioning**: setiap perubahan tersimpan sebagai versi baru dan versi sebelumnya otomatis menjadi **Tidak Aktif**. Hanya versi aktif yang dapat diedit, sedangkan surat lama tetap memakai format saat surat itu dibuat.
- Halaman **Preview** menampilkan simulasi surat utuh untuk versi aktif maupun versi lama, dengan pilihan kop surat milik unit template.
- Template yang sudah dipakai surat tidak dapat dihapus.

#### Jenis input pada form Buat Surat

Form Buat Surat dibuat otomatis dari placeholder template. Jenis input ditentukan oleh pola nama field melalui `SuratFieldResolver`, dan dibuat konsisten di seluruh template agar pengguna tidak bingung:

| Pola nama field | Perilaku pada form Buat Surat |
|---|---|
| Mengandung kata `tanggal` | Pemilih tanggal (date picker) |
| Mengandung kata `penandatangan` atau `hrd` | Dropdown pengguna pada unit yang sama |
| `nama_karyawan`, nama pembimbing/dosen/atasan, dan nama anggota | Input dengan saran nama (autocomplete) |
| Nominal uang (misalnya `estimasi_biaya`, `nilai_kontrak`, `besaran_gaji`) | Input rupiah dengan awalan "Rp" dan pemisah ribuan otomatis, serta petunjuk pengisian |
| Estimasi waktu pengerjaan dan sejenisnya | Isian jumlah beserta pilihan satuan waktu (hari, minggu, bulan, dan seterusnya) |
| Pilihan platform atau opsi ganda | Kotak centang (checkbox) |
| Pilihan tetap, atau pilihan dengan opsi "Lainnya" | Dropdown, dengan kolom isian tambahan bila memilih "Lainnya" |
| Bulan dan rentang tanggal | Pemilih bulan dan pemilih rentang tanggal |
| Keterangan panjang (misalnya `ringkasan_fitur`) | Kolom teks multibaris disertai contoh pengisian |
| Daftar berulang: `item_N_*`, `barang_N_*`, `layanan_N_*`, `peserta_N_*`, `anggota_N_*` | Dikelompokkan per baris (Item 1, Item 2, ...). Baris ke-2 dan seterusnya bersifat **opsional**, dan bila dikosongkan ditampilkan sebagai "-" pada surat |
| Lainnya | Input teks biasa |

Template juga mendukung **lampiran terpisah halaman** dengan menyisipkan penanda berikut di antara isi utama dan isi lampiran:

```html
<div style="page-break-before: always;"></div>
```

### 5. Buat Surat (Admin dan User)

Alur pembuatan surat:

1. Pilih template dari daftar milik unit sendiri (dilengkapi pencarian langsung).
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

- **Buat Surat Baru** - pemilihan template.
- **Riwayat Surat Saya** - daftar surat yang dibuat oleh pengguna yang sedang login, lengkap dengan pencarian, filter tanggal, filter template, serta aksi lihat, edit, cetak, dan hapus.

**Buku kontak.** Nama dan nomor kontak yang pernah diisi pada kolom pembimbing, atasan, atau anggota tim disimpan ke tabel `kontak`, sehingga pada penulisan surat berikutnya cukup mengetik sebagian nama dan data kontaknya terisi otomatis.

### 6. Preview, Cetak, dan PDF

Tiga keluaran ini dijaga agar **identik**: ukuran kertas A4 (210 x 297 mm) dengan margin 30 mm di keempat sisi.

- **Preview** - tampilan surat di dalam aplikasi.
- **Cetak** - halaman siap cetak yang langsung membuka dialog print browser.
- **PDF Arsip** - dibuat otomatis oleh sistem dan tersimpan ke storage.

PDF arsip di-generate ulang secara otomatis pada tiga kondisi: saat surat dibuat, saat isi surat diubah, dan saat surat ditandatangani secara digital.

### 7. Tanda Tangan Digital

- Surat yang disetujui memperoleh QR Code yang mengarah ke halaman detail surat, sehingga keaslian surat dapat diverifikasi dengan memindainya.
- QR Code digenerate secara lokal oleh server, bukan mengambil dari layanan luar, agar tetap muncul konsisten pada Preview, Cetak, maupun PDF.
- Surat yang belum disetujui menampilkan keterangan "(Belum Menyetujui/Ditandatangani)".
- Persetujuan hanya dapat dilakukan oleh pengguna yang ditunjuk sebagai penandatangan, atau oleh Admin.

### 8. Pengaturan Kop Surat

- **Super Admin** membuat kop baru dan memilih unit kerja pemiliknya. Super Admin dapat mengubah dan menghapus kop pada semua unit, yang tampil dikelompokkan per unit.
- **Admin** tidak dapat membuat kop baru (tombol tambah tidak tampil), tetapi dapat mengubah dan menghapus kop milik unitnya sendiri.
- Mendukung **versioning** seperti Template Surat: menyimpan perubahan akan membuat versi baru dan menonaktifkan versi sebelumnya, sehingga surat lama tetap memakai kop surat yang berlaku saat itu. Hanya versi aktif yang dapat diedit, sedangkan versi lama ditandai **Tidak Aktif** dan hanya dapat dilihat.
- Tersedia **Live Preview** saat mengisi form, serta **halaman Preview** untuk melihat kop surat versi aktif maupun versi lama pada simulasi surat utuh.
- Logo yang diunggah **otomatis diseragamkan** ke kanvas 400 x 150 piksel dengan latar putih, sehingga proporsi kop surat konsisten berapapun ukuran file aslinya.
- Kop yang sudah dipakai surat tidak dapat dihapus. Bila versi aktif dihapus, versi terbaru yang tersisa dijadikan aktif. File logo hanya ikut dihapus bila tidak dipakai kop lain.
- Pilihan kop pada form Buat Surat hanya menampilkan kop aktif milik unit pengguna.

### 9. Arsip Digital

Terbagi menjadi dua folder:

- **Surat Keluar** - seluruh surat yang dibuat melalui sistem, otomatis masuk ke sini beserta file PDF-nya.
- **Surat Masuk** - arsip dokumen dari pihak luar, diinput manual (nomor surat pengirim, instansi asal, perihal, tanggal, dan file).

Fitur pendukung: pencarian, filter tanggal, filter template, filter status file, dan filter cakupan surat (semua surat di unit atau surat buatan sendiri, khusus Admin).

Aksi yang tersedia: lihat detail, cetak, ACC dan tanda tangan digital, unggah/ganti file arsip, serta hapus surat.

User hanya melihat surat miliknya sendiri pada halaman ini. Admin melihat seluruh surat pada unitnya.

### 10. Kelola Pengguna dan Role

**Kelola Pengguna** menampilkan nama, email, role, **unit kerja** (badge berwarna), dan status akun.

*Super Admin:*

- Melihat seluruh akun dan dapat memfilter berdasarkan unit kerja.
- Menambah akun baru dengan memilih role dan unit kerja.
- Mengubah nama, email, role, unit kerja, dan mereset password pengguna lain tanpa perlu mengetahui password lama (dikosongkan bila tidak ingin diubah).
- Menonaktifkan dan mengaktifkan kembali akun.
- Menghapus permanen, tersedia hanya bila pengguna belum pernah tercatat pada surat manapun.
- Mengelola role pada tab **Role dan Hak Akses**.

*Admin:*

- Hanya melihat akun pada unit kerjanya (tanpa Super Admin dan Admin lain).
- Hanya dapat **mengubah nama** serta **menonaktifkan/mengaktifkan** akun.

**Kelola Role dan Hak Akses (Super Admin):**

- Membuat role baru dan mengatur hak akses per fitur melalui checkbox. Menu pada sidebar role tersebut mengikuti hak akses yang dicentang.
- Menampilkan jumlah pengguna yang memakai tiap role.
- Role yang masih dipakai pengguna tidak dapat dihapus.

### 11. Log Aktivitas Sistem

Audit trail yang mencatat aksi penting beserta pelaku, unit kerja, dan waktunya. Modul yang tercatat meliputi Surat, Surat Masuk, Template Surat, Kop Surat, Pengguna, dan Role.

Log bersifat **hanya-baca** - tidak ada fitur edit maupun hapus, sehingga jejak aktivitas tetap dapat dipercaya sebagai bukti.

- **Super Admin** melihat log seluruh unit.
- **Admin** hanya melihat log unit kerjanya. Unit yang baru dibuat memiliki log kosong sampai ada aktivitas di dalamnya. Aktivitas Super Admin tidak ditampilkan kepada Admin.

Data sensitif seperti password tidak pernah dicatat pada log. Halaman log dilengkapi pencarian, filter aksi, filter modul, dan filter rentang tanggal.

### 12. Evaluasi Retensi Arsip (SPK)

Fitur pendukung keputusan untuk menentukan masa simpan arsip menggunakan metode **Hybrid AHP + TOPSIS**, dijalankan **per unit kerja**. AHP menghasilkan bobot kriteria, lalu TOPSIS memakai bobot itu untuk merangking arsip.

**Kriteria penilaian:**

| Kode | Kriteria | Tipe | Nilai yang dipakai |
|---|---|---|---|
| K1 | Nilai Guna Dokumen | Benefit | Hukum = 4, Keuangan = 3, Operasional = 2, Pemberitahuan = 1 |
| K2 | Sifat/Kerahasiaan Surat | Benefit | Sangat Rahasia = 3, Rahasia = 2, Biasa/Terbuka = 1 |
| K3 | Masa Simpan/Umur Surat | Cost | Umur surat dalam hari sejak tanggal surat |
| K4 | Frekuensi Akses/Download | Benefit | Jumlah akses surat |

K3 bertipe *cost*, artinya semakin lama sebuah arsip tersimpan, semakin rendah skornya dan semakin condong ke rekomendasi Musnah. K4 dihitung otomatis dari jumlah akses surat, sedangkan K1 dan K2 diisi manual oleh pengguna.

**Bobot kriteria (AHP).** Admin mengisi perbandingan berpasangan antar kriteria (skala Saaty 1 sampai 9) pada tab Kelola Kriteria Retensi. Sistem membentuk matriks perbandingan, menghitung bobot dari *eigenvector* utama, lalu menguji konsistensi dengan CI = (λmaks − n) / (n − 1) dan CR = CI / RI (RI = 0,90 untuk n = 4). Penilaian hanya disimpan bila **CR ≤ 0,10**; bila tidak konsisten, bobot lama tetap dipakai. Bobot tidak dapat diisi manual. Setiap unit kerja memiliki matriks dan bobotnya sendiri, dan perubahan pada satu unit tidak memengaruhi unit lain. Setelah bobot disimpan, seluruh arsip unit itu dihitung ulang.

Matriks bawaan dari seeder (`RetensiScoringService::PERBANDINGAN_AWAL`) menghasilkan bobot sekitar K1 0,483, K2 0,272, K3 0,157, dan K4 0,088 (CR ≈ 0,005). Nilai ini hanya titik awal dan sebaiknya diganti dengan penilaian Admin tiap unit.

**Perangkingan (TOPSIS).** Untuk arsip yang sudah dinilai dalam satu unit, sistem melakukan normalisasi vektor, normalisasi terbobot, menentukan solusi ideal positif dan negatif (benefit = maksimum, cost = minimum), menghitung jarak D⁺ dan D⁻, lalu nilai kedekatan relatif C<sub>i</sub> = D⁻ / (D⁺ + D⁻). Nilai C<sub>i</sub> (0 sampai 1) disimpan sebagai skor. Bila hanya ada satu arsip atau semua arsip bernilai sama, C<sub>i</sub> tidak dapat dibedakan dan diberi nilai 0,5. Seluruh langkah perhitungan dapat dilihat Admin pada halaman **Detail Perhitungan**.

**Klasifikasi hasil skor:**

| Nilai C<sub>i</sub> | Rekomendasi |
|---|---|
| ≥ 0,70 | Permanen |
| 0,40 sampai di bawah 0,70 | Simpan Lokal |
| < 0,40 | Musnah |

**Alur evaluasi:**

1. Pengguna mengisi Nilai Guna dan Kerahasiaan pada surat, lalu menyimpan.
2. Sistem langsung menghitung ulang skor seluruh arsip yang telah dinilai **dalam unit yang sama**. Perhitungan bersifat relatif antar arsip, sehingga penambahan data baru memengaruhi skor arsip lain di unit tersebut.
3. Hasil berstatus **Menunggu Validasi** hingga disetujui Admin.
4. Admin melakukan validasi, dan status berubah menjadi **Tervalidasi**.

**Pembatasan tampilan:** User tidak melihat kolom skor maupun status rekomendasi (Musnah/Simpan Lokal/Permanen). User hanya melihat status prosesnya: Belum Dievaluasi, Menunggu Validasi, atau Tervalidasi. Keputusan retensi merupakan wewenang administratif Admin.

Halaman ini dilengkapi pencarian, filter tanggal, filter template, filter status evaluasi, dan pengurutan (tanggal, jumlah akses, serta skor untuk Admin).

---

## Alur Kerja Sistem

### Alur Surat Keluar

```
Super Admin menyiapkan Template Surat dan Kop Surat untuk tiap unit kerja
                    |
                    v
Admin/User unit memilih template milik unitnya dan mengisi data surat
                    |
                    v
Sistem membuat nomor surat otomatis + menyimpan isian surat
                    |
                    v
Sistem menghasilkan PDF dan menyimpannya ke Arsip Digital unit
                    |
                    v
Penandatangan (atau Admin) melakukan ACC & TTD Digital
                    |
                    v
PDF diperbarui dengan QR Code verifikasi; surat berstatus Disetujui
                    |
                    v
Surat dievaluasi masa simpannya pada menu Retensi Arsip (per unit)
```

### Alur Surat Masuk

```
Dokumen diterima dari pihak luar
                    |
                    v
Admin/User membuka Arsip Digital > folder Surat Masuk
                    |
                    v
Input nomor surat pengirim, instansi asal, perihal, tanggal, dan unggah file
                    |
                    v
Tersimpan ke arsip unit dan dapat dicari sewaktu-waktu
```

### Alur Pengelolaan Akun

```
Super Admin membuat akun, memilih role dan unit kerja
                    |
                    v
Pengguna login dan hanya melihat data unit kerjanya
                    |
                    v
Admin unit dapat mengubah nama dan menonaktifkan akun di unitnya
                    |
                    v
Lupa password: pengguna mengirim permintaan -> notifikasi ke Super Admin -> reset manual
```

---

## Struktur Database

| Tabel | Keterangan |
|---|---|
| `unit_kerja` | Daftar unit kerja (Kantor Pusat, Inovindo Web, Inovindo Academy) |
| `users` | Data pengguna, memiliki `role_id`, `unit_kerja_id`, dan status `is_aktif` |
| `roles` | Daftar role (Super Admin, Admin, User, dan role tambahan) |
| `permissions` | Daftar hak akses per fitur |
| `role_permissions` | Tabel pivot penghubung role dengan hak akses |
| `template_surat` | Template surat beserta nomor versi, status aktif, dan `unit_kerja_id` |
| `surat` | Data surat keluar maupun surat masuk, beserta `unit_kerja_id` |
| `isi_surat` | Isian field surat dengan pola EAV (`nama_field` dan `nilai_field`) |
| `pengaturan_kop_surat` | Kop surat beserta versi, status aktif, dan `unit_kerja_id` |
| `log_aktivitas` | Catatan audit trail aktivitas sistem, beserta `unit_kerja_id` |
| `retensi_kriteria` | Kriteria K1 sampai K4 beserta bobot (hasil AHP) dan tipe, terpisah per `unit_kerja_id` |
| `kriteria_ahp` | Matriks perbandingan berpasangan AHP beserta λmaks, CI, RI, dan CR, satu baris per `unit_kerja_id` |
| `evaluasi_retensi` | Hasil penilaian, skor (nilai kedekatan relatif C<sub>i</sub>), jarak D⁺ dan D⁻, rekomendasi, dan status validasi |
| `kontak` | Buku kontak untuk saran isian nama pembimbing, atasan, dan anggota |
| `notifications` | Notifikasi bawaan Laravel, dipakai untuk permintaan reset password |

### Relasi Utama

```
unit_kerja 1 ---- N users, template_surat, pengaturan_kop_surat,
      |                   surat, log_aktivitas, retensi_kriteria kriteria_ahp
      |
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

Catatan:

- Kolom `template_id` dan `kop_surat_id` pada tabel `surat` bersifat *nullable*, karena surat masuk dari pihak luar tidak memakai template maupun kop surat perusahaan.
- Kolom `unit_kerja_id` pada `users` bernilai `NULL` untuk Super Admin, karena Super Admin tidak terikat unit.
- Relasi surat ke template dan kop tidak ikut difilter per unit, sehingga surat lama tetap dapat menampilkan template dan kop-nya meskipun Super Admin kemudian memindahkan unit template tersebut.

---

## Instalasi dan Konfigurasi

### Prasyarat

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan NPM
- MySQL/MariaDB
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
git clone https://github.com/Rainn-commits/LetterTide.git
cd LetterTide

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

Sesuaikan konfigurasi database pada file `.env`:

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

Buat database kosong dengan nama yang sama (misalnya `lettertide_db`) sebelum menjalankan migrasi.

### Migrasi dan Data Awal

```bash
# 6. Buat seluruh tabel
php artisan migrate

# 7. Isi data awal: role, unit kerja, hak akses, kriteria retensi, dan akun contoh
php artisan db:seed
```

Pada instalasi baru, template surat dan kop surat dibuat oleh Super Admin melalui aplikasi, dengan memilih unit kerja pemiliknya.

**Memperbarui database yang sudah berisi data (dari versi sebelum unit kerja):**

```bash
php artisan migrate
php artisan db:seed
php artisan optimize:clear
```

Migrasi akan menambahkan kolom unit kerja, mengisi data lama (surat dan log mengikuti unit pembuatnya, kop lama masuk Kantor Pusat, template dibagi menurut nama), menyalin bobot retensi untuk tiap unit, serta menyesuaikan hak akses tiga role bawaan sesuai tabel pada bagian Struktur Hak Akses. Role buatan sendiri tidak diubah. **Cadangkan database terlebih dahulu.**

### Konfigurasi Zona Waktu

Pastikan `config/app.php` memakai zona waktu Indonesia agar tanggal dan jam pada Log Aktivitas serta surat sesuai WIB:

```php
'timezone' => 'Asia/Jakarta',
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

Seeder membuat sepuluh akun awal untuk keperluan pengujian, tiga di antaranya Admin (satu per unit kerja):

| Email | Nama | Role | Unit Kerja |
|---|---|---|---|
| superadmin@gmail.com | Super Admin | Super Admin | - (semua unit) |
| admin@gmail.com | Admin Kantor Pusat | Admin | Kantor Pusat |
| user1@gmail.com | User Pusat 1 | User | Kantor Pusat |
| user2@gmail.com | User Pusat 2 | User | Kantor Pusat |
| admin.web@gmail.com | Admin Inovindo Web | Admin | Inovindo Web |
| user3@gmail.com | User Web 1 | User | Inovindo Web |
| user4@gmail.com | User Web 2 | User | Inovindo Web |
| admin.academy@gmail.com | Admin Inovindo Academy | Admin | Inovindo Academy |
| user5@gmail.com | User Academy 1 | User | Inovindo Academy |
| user6@gmail.com | User Academy 2 | User | Inovindo Academy |

Password masing-masing akun dapat dilihat dan diubah pada `database/seeders/DatabaseSeeder.php`.

**Penting:** seeder menimpa nama, password, role, dan unit kerja kesepuluh akun di atas setiap kali dijalankan, termasuk password yang mungkin sudah diubah melalui aplikasi. Seeder hak akses (`RolePermissionSeeder`) hanya menambahkan dan tidak pernah menghapus, sehingga pengaturan role yang sudah diubah Super Admin tidak tertimpa.

**Peringatan:** Seeder ditujukan untuk instalasi awal atau pengembangan. Hindari menjalankan `php artisan db:seed` atau `php artisan migrate --seed` pada database yang sudah berisi data aktif tanpa mencadangkannya terlebih dahulu.

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
│   │   ├── PenggunaController.php    Kelola pengguna per unit
│   │   ├── RoleController.php
│   │   ├── LogAktivitasController.php
│   │   ├── EvaluasiRetensiController.php
│   │   ├── RetensiKriteriaController.php
│   │   ├── UserLookupController.php  Pencarian nama pengguna (sebatas unit)
│   │   ├── KontakLookupController.php  Saran isian dari buku kontak
│   │   └── NotificationController.php
│   └── Middleware/                   Middleware active dan permission
├── Models/
│   ├── Concerns/
│   │   └── BerdasarkanUnitKerja.php  Trait pemisah data per unit kerja
│   ├── User.php  Role.php  Permission.php  UnitKerja.php
│   ├── Surat.php  IsiSurat.php  TemplateSurat.php  Kontak.php
│   ├── PengaturanKopSurat.php  LogAktivitas.php
│   └── RetensiKriteria.php  EvaluasiRetensi.php
├── Notifications/
│   └── ResetPasswordRequested.php    Notifikasi permintaan reset password
└── Services/
    ├── PlaceholderParser.php         Parsing dan render placeholder template
    ├── SuratFieldResolver.php        Penentu jenis input form surat dari nama field
    └── RetensiScoringService.php     Perhitungan skor retensi arsip

database/
├── migrations/                       Termasuk migrasi unit_kerja dan unit_kerja_id
└── seeders/                          Role, unit kerja, hak akses, kriteria, dan akun contoh

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
│   ├── password-toggle.js            Tombol tampilkan/sembunyikan password
│   └── dashboard-interactions.js
└── views/
    ├── layouts/app.blade.php         Kerangka utama aplikasi
    ├── partials/                     Sidebar, topbar, dan pilihan unit kerja
    ├── surat/                        pilih-template, form, preview, cetak, pdf,
    │   └── partials/                 edit, arsip, riwayat; field-input (input dinamis)
    ├── kop-surat/  template-surat/  pengguna/  role/
    └── evaluasi-retensi/  retensi-kriteria/  log-aktivitas/
```

---

## Catatan Teknis

### Pemisahan Data per Unit (Trait `BerdasarkanUnitKerja`)

Trait ini memasang *global scope* pada model sehingga setiap query otomatis ditambah kondisi `unit_kerja_id` milik pengguna yang sedang login:

- Pengguna **lintas unit** (punya `role.manage`) tidak difilter.
- Pengguna Admin/User hanya mendapatkan data unitnya. Akun tanpa unit tidak mendapatkan data apa pun.
- Tanpa pengguna login (seeder, tinker, artisan) tidak ada filter.
- Saat data dibuat oleh Admin/User, kolom `unit_kerja_id` terisi otomatis dari unit pembuatnya.
- Daftar pengguna dibatasi dengan scope `User::diUnitSaya()`, dipakai pada Kelola Pengguna, daftar penandatangan, pencarian nama, dan dashboard Admin.

### Pengecekan Hak Akses

- Pada route: `->middleware('permission:kode')` atau `->middleware('permission:kode1,kode2')` (salah satu cukup).
- Pada kode: `$user->punyaPermission('kode')`, `$user->punyaSalahSatuPermission([...])`, dan `$user->isGlobal()`.
- Sidebar menampilkan sebuah menu bila role termasuk daftar bawaan menu tersebut atau memiliki hak aksesnya di database, dan menyembunyikannya bila role sudah memiliki daftar hak akses tetapi tanpa hak akses menu itu.

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

- Template surat belum mendukung baris tabel dinamis yang dapat ditambah tanpa batas. Daftar berulang memakai jumlah baris tetap (misalnya `item_1_*` sampai `item_N_*`), dengan baris kedua dan seterusnya bersifat opsional.
- Reset password bersifat manual oleh Super Admin, belum melalui email otomatis.
- Unit kerja ditentukan lewat seeder (`UnitKerjaSeeder`). Belum ada menu untuk menambah atau mengubah unit dari dalam aplikasi.
- Rekomendasi retensi arsip berhenti pada tahap rekomendasi dan validasi; sistem belum melakukan eksekusi pemusnahan arsip secara otomatis.
- Skor TOPSIS bersifat relatif terhadap arsip lain dalam unit yang sama, sehingga skor sebuah arsip dapat berubah ketika arsip baru dinilai. Ambang 0,70 dan 0,40 merupakan ketentuan rancangan sistem, bukan bagian dari metode TOPSIS.

---

## Informasi Project

| Keterangan | Detail |
|---|---|
| Nama Aplikasi | LetterTide |
| Instansi | PT Inovindo Digital Media, Bandung |
| Jenis | Kerja Praktik (KP) |
| Institusi | Universitas PGRI Madiun |
| Program Studi | Teknik Informatika |
