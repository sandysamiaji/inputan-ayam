# Project Input — Nochi Farm Input (Manajemen Operasional Kandang)

> **Dokumen analisis lengkap aplikasi `nochifarminput`**, disusun untuk pemilik yang akan menjual aplikasi dan untuk calon pembeli/analis.
> Dibuat dari pembacaan kode (route, controller, service, model, observer, migration, seeder, view Blade, test, `.env`, log) pada 2026-11-11.
> **Aplikasi belum dijalankan di browser dan test suite sengaja tidak dijalankan** (alasan di bagian 17.3). Hal yang belum dibuktikan lewat uji jalan ditandai `[verifikasi]`. Angka dan rumus di bagian 7 dikutip dari kode, bukan dari tebakan.
> **Versi ini memperbaiki dokumen sebelumnya**: jumlah izin sebenarnya **64** (bukan 84); pengecekan admin di Rekap ternyata **dinonaktifkan**; `/seed-standards` terbukti **menghapus** seluruh standar; ada 15 temuan baru (bagian 15).

---

## Daftar isi

1. [Ringkasan eksekutif](#1-ringkasan-eksekutif)
2. [Arsitektur dan teknologi](#2-arsitektur-dan-teknologi)
3. [Peran dan sistem hak akses](#3-peran-dan-sistem-hak-akses)
4. [Inventaris halaman dan komponen](#4-inventaris-halaman-dan-komponen)
5. [Data master dan data awal](#5-data-master-dan-data-awal)
6. [Kamus data dan relasi tabel](#6-kamus-data-dan-relasi-tabel)
7. [Aturan bisnis dan rumus](#7-aturan-bisnis-dan-rumus)
8. [Lokasi, GPS, foto, peta](#8-lokasi-gps-foto-peta)
9. [Daftar lengkap 64 izin](#9-daftar-lengkap-64-izin)
10. [Ekspor, cetak, impor, audit, restore](#10-ekspor-cetak-impor-audit-restore)
11. [Integrasi dan tabel bersama](#11-integrasi-dan-tabel-bersama)
12. [Alur kerja per peran dan skenario demo](#12-alur-kerja-per-peran-dan-skenario-demo)
13. [Matriks fitur](#13-matriks-fitur)
14. [Penilaian kelayakan jual](#14-penilaian-kelayakan-jual)
15. [Temuan masalah dan risiko](#15-temuan-masalah-dan-risiko)
16. [Yang bisa dibanggakan dan batasan yang harus diakui](#16-yang-bisa-dibanggakan-dan-batasan-yang-harus-diakui)
17. [Instalasi, deploy, dan pengujian](#17-instalasi-deploy-dan-pengujian)
18. [Checklist sebelum dijual](#18-checklist-sebelum-dijual)
19. [Rencana perbaikan](#19-rencana-perbaikan)
20. [Untuk analisis lanjutan dan keterbatasan dokumen](#20-untuk-analisis-lanjutan-dan-keterbatasan-dokumen)

---

## 1. Ringkasan eksekutif

| Item | Isi |
|---|---|
| Nama | Nochi Farm Input — pencatatan harian peternakan ayam petelur, gudang, rekap, standar produksi, audit |
| Masalah yang diselesaikan | Pemilik farm sulit memastikan data harian kandang benar: telur yang dilaporkan vs yang seharusnya, pakan yang dipakai vs standar, siapa mengubah apa, stok gudang riil |
| Pengguna | Petugas kandang (input lewat HP), supervisor, admin/pemilik |
| Framework | Laravel 10 (PHP ^8.1; terpasang PHP 8.1.25 di mesin ini), Sanctum (tidak dipakai API), PhpSpreadsheet, Blade |
| Antarmuka | Blade + **Tailwind CSS via CDN**, Lucide, Chart.js (CDN), Google Fonts. Mobile-first, tema merah marun |
| Database | MySQL, DB `nochifram` — **dipakai bersama** dengan nochifram (penjualan), nochifarmpengeluaran, companynochifarm |
| Skala | 8 controller (± 6,6 ribu baris), 6 service, 16 model, 1 observer, 1 middleware, 18 migration, 6 seeder, 21 view Blade (± 19 ribu baris) |
| Tes | 28 tes bermakna (+2 `ExampleTest` bawaan) — **berjalan di database asli**, lihat 17.3 |
| Git | Repo git aktif, 3 commit terakhir: "perbaikan", "perubahan bisa melihat masa depan", "coba benerin data" |
| Skor kelayakan | **± 2,8 / 5** (turun dari 3,1 setelah analisis lebih dalam, bagian 14) |
| Estimasi harga | Rp 25–60 juta (apa adanya) · Rp 60–100 juta (setelah perbaikan bagian 18) · lihat bagian 14 |

**Satu kalimat nilai jual:** aplikasi yang membuat setiap angka harian kandang bisa diadu dengan standar umur ayam (HD, pakan, berat telur, bobot badan), dengan jejak audit dan hak akses per petugas.

**Satu kalimat peringatan jujur:** fiturnya sudah lengkap, tetapi ada celah keamanan, konstanta konversi peti yang tidak konsisten, dan data yang bisa melenceng (populasi) — semuanya harus ditutup sebelum dijual sebagai produk.

---

## 2. Arsitektur dan teknologi

### 2.1 Susunan kode

| Lapisan | Isi |
|---|---|
| Controller | [DashboardController](../app/Http/Controllers/DashboardController.php) (1.210 baris: dashboard + 5 aksi input), [WarehouseController](../app/Http/Controllers/WarehouseController.php) (2.019), [RekapController](../app/Http/Controllers/RekapController.php) (970), [MasterController](../app/Http/Controllers/MasterController.php) (856), [AuditController](../app/Http/Controllers/AuditController.php) (212), [AuthController](../app/Http/Controllers/AuthController.php) (116), [InputController](../app/Http/Controllers/InputController.php) (111), [ProfileController](../app/Http/Controllers/ProfileController.php) (62) |
| Service | [PermissionService](../app/Services/PermissionService.php) (registri izin), [AuditService](../app/Services/AuditService.php), [ProductionStandardService](../app/Services/ProductionStandardService.php), [MedicineCatalogService](../app/Services/MedicineCatalogService.php) (katalog 26 obat + stok), [OutboundIntegrationService](../app/Services/OutboundIntegrationService.php) (baca penjualan nochifram), [ExcelRestoreService](../app/Services/ExcelRestoreService.php) (862 baris) |
| Model | `AuditLog, Coop, EggProduction, FarmStock, FeedConsumption, Flock, HealthTreatment, Mortality, Quarantine, Sale, SaleItem, Setting, User, UserPermission, WeeklyStandard, WeightSample` |
| Observer | [AuditObserver](../app/Observers/AuditObserver.php), didaftarkan di [AppServiceProvider](../app/Providers/AppServiceProvider.php) untuk 13 model |
| Middleware | [CheckPermission](../app/Http/Middleware/CheckPermission.php) (alias `permission:`) |
| View | `layouts/app`, `auth/login`, `dashboard`, `input/index`, `warehouse/{index,telur,pakan,obat,karantina}`, `rekap/{index,detail}`, `master/{index,flocks,standards,settings,info-farm,medicines,permissions,audit}`, `profile/index`, `welcome` |

### 2.2 Pola yang dipakai
- Izin berbasis **kunci string** + middleware `permission:kunci` di route, dan `canAccess('kunci')` di Blade untuk menyembunyikan kartu/tombol.
- **Audit otomatis** lewat Observer (create/update/delete) ke tabel `audit_logs`, dengan snapshot data untuk restore.
- Banyak logika bisnis ada di controller (angka dashboard, rekap, gudang). Service dipakai untuk izin, audit, standar, obat, restore, integrasi.
- Migration defensif (`if !hasTable`, `hasColumn`) karena DB dipakai bersama.
- Beberapa model membuat tabel/kolom **saat runtime** (`ensureTableExists`, `ensureColumnsExist`): lihat temuan 15 poin 13.

### 2.3 Ketergantungan luar (internet)
| Sumber | Dipakai untuk | Dampak bila tidak ada sinyal |
|---|---|---|
| `cdn.tailwindcss.com` | Seluruh gaya tampilan (Tailwind versi Play CDN, bukan build) | **Halaman tampil tanpa gaya** `[verifikasi]` |
| `unpkg.com/lucide@latest` | Ikon (versi tidak dikunci) | Ikon hilang, versi bisa berubah sendiri |
| `cdn.jsdelivr.net/npm/chart.js` | Grafik gudang dan rekap (versi tidak dikunci) | Grafik kosong |
| `fonts.googleapis.com` | Font Plus Jakarta Sans | Font cadangan |

Tidak ada service worker/manifest PWA di proyek. Kandang dengan sinyal lemah adalah risiko nyata bagi aplikasi yang diklaim "mobile".

---

## 3. Peran dan sistem hak akses

### 3.1 Peran
| Peran | Kolom `users.role` | Akses |
|---|---|---|
| Admin | `admin` | Semua fitur. [PermissionService::canAccess](../app/Services/PermissionService.php) langsung `true` |
| User (petugas/supervisor) | `user` | Sesuai tabel `user_permissions`; bila belum diatur, memakai **default** registri (kolom "Default" bagian 9) |
| Nonaktif | `is_active = 0` | Ditolak login dan semua izin `false` |

### 3.2 Cara kerja
1. Registri **64 izin** dalam **10 kelompok** didefinisikan di `PermissionService::getAllPermissions()` (kelompok bernomor 1–4 dan 6–11; **tidak ada kelompok 5**, nomor loncat).
2. Admin mengatur izin per user lewat toggle di `/master/hak-akses` (satu-satu atau massal per kelompok). Disimpan di `user_permissions (user_id, permission_key, is_enabled)` unik per pasangan.
3. `canAccess(user, kunci)`: nonaktif → false · admin → true · ada baris izin → nilainya · tidak ada baris → **default registri** · kunci tidak dikenal → **`true`**.
4. Route dijaga `permission:kunci`; Blade memakai `@if(!auth()->check() || auth()->user()->canAccess('kunci'))`.
5. Respons gagal: AJAX → JSON 403, lainnya → redirect ke dashboard dengan pesan "Akses Ditolak".

### 3.3 Masalah pada sistem izin (terverifikasi dari kode)
| # | Temuan | Dampak |
|---|---|---|
| a | **Kunci tidak dikenal = diizinkan** (fail-open), baris terakhir `canAccess` mengembalikan `true` | Salah ketik kunci di route membuka fitur untuk semua |
| b | Route gudang `store/update/toggle-status/destroy` memakai kunci **`feature_warehouse_manage` yang tidak ada di registri** | Karena poin a, **semua user yang boleh membuka Gudang bisa tambah/ubah/hapus mutasi stok**. Toggle "Aksi Edit & Hapus Mutasi" (`warehouse_btn_manage`) hanya menyembunyikan tombol, bukan menutup route |
| c | 7 kunci terdaftar tetapi **tidak dipakai di mana pun**: `feature_master_info_farm`, `feature_master_coops`, `feature_master_standards`, `feature_master_settings`, `feature_master_permissions`, `feature_rekap_view`, `warehouse_btn_add` | Sub-halaman Master hanya dijaga `menu_master`. Toggle di layar hak akses terlihat berfungsi padahal tidak berpengaruh |
| d | Aksi tulis di `/master/*` (kloter, blok, standar, pengaturan, info farm, restore Excel) hanya dijaga `menu_master`; hanya aksi **pengguna** (`storeUser`, `updateUser`, toggle izin) mengecek `role === 'admin'` di dalam fungsi | User non-admin yang diberi `menu_master` bisa mengubah master penting |
| e | `RekapController::requireAdmin()` **kosong** ("dinonaktifkan atas permintaan") | Dokumen lama menulis "ada pengecekan admin" — **salah**. Edit/hapus data historis hanya dijaga `feature_rekap_manage` (default tidak) |
| f | `input_filter_tanggal` default **ya**, validasi tanggal hanya `date` tanpa batas | Petugas bisa menginput untuk tanggal lampau/masa depan; jejaknya ada di audit, tetapi tidak dicegah |

---

## 4. Inventaris halaman dan komponen

Notasi izin: `[kunci]`. Semua halaman (kecuali login) wajib login.

### 4.1 Login — `/login`  ([auth/login.blade.php](../resources/views/auth/login.blade.php))
- Kolom: *Username atau Email*, *Password*, centang *Ingat saya*.
- Pesan: "Username atau Email wajib diisi." · "Password wajib diisi." · "Username atau Password yang dimasukkan tidak sesuai." · "Akun Anda dinonaktifkan oleh Administrator…".
- Proses: coba `Auth::attempt` (email atau username), cadangan cari user manual dan `Hash::check`. Login dan logout dicatat di audit.
- **Tidak ada pembatasan percobaan login, tidak ada lupa password.**

### 4.2 Dashboard — `/`  `[menu_dashboard]`  ([dashboard.blade.php](../resources/views/dashboard.blade.php), 2.661 baris)

| Bagian | Isi | Izin |
|---|---|---|
| Sapaan | "Selamat pagi/siang/sore/malam" (jam 04–11, 11–15, 15–18, lainnya), nama petugas, tanggal Indonesia | — |
| Filter | Pemilih kloter, pemilih tanggal (boleh mundur dan maju) | `dash_filter`, `dash_filter_tanggal` |
| Banner info | Pesan motivasi dan pesan kondisi ayam (otomatis dari standar umur, atau teks manual), dengan jadwal tampil (default 06:00–18:00) dan saklar aktif | — |
| Kartu kloter | Total kloter, total blok, populasi aktif, umur, persentase populasi per kloter, modal rinci `modalPopulasiKloter` | `dash_card_flock` |
| Kartu telur | Panen hari ini (peti dan butir) | `dash_card_egg` |
| Kartu pakan | Pemakaian hari ini (kg, jenis) | `dash_card_feed` |
| Kartu mortalitas | Mati + afkir hari ini; klik → Gudang Karantina/Mortalitas | `dash_card_mortality`, `dash_card_mortality_click` |
| Kartu bobot | Ringkasan sampel bobot 6 blok; klik → modal `modalBobot6Blok` (evaluasi vs standar) | `dash_card_weight`, `dash_card_weight_click` |
| Kartu vaksin/obat | Jumlah kegiatan medis hari ini | `dash_card_health` |
| Kartu karantina | Ayam sakit yang sedang dikarantina; klik → Gudang Karantina | `dash_card_quarantine`, `dash_card_quarantine_click` |
| **Kartu blok kandang** (A–F) | Per blok: badge **HD%** (hijau jika ≥ target umur, merah jika di bawah, "Belum Input" bila kosong), badge fase umur (klik → `modalFasePenjelasan`), kapasitas aktif vs kapasitas dan persen, HD kloter, target HD master, **3 titik sampel bobot ayam dan telur** (Capai/Kurang), **Realisasi Panen Telur vs Perkiraan**, **Kebutuhan dan Realisasi Pakan pagi/sore**, nama petugas penginput | `dash_section_coops`, `dash_coop_hd`, `dash_coop_standards`, `dash_coop_egg_comparison`, `dash_coop_feed_comparison`, `dash_coop_fase_info` |
| Kolom gudang (desktop) | Stok telur terkini/masuk/keluar, stok pakan terkini/konsumsi/terjual | `dash_section_warehouse_summary`, `dash_widget_egg_stock`, `dash_widget_feed_stock` |
| Timeline aktivitas | Gabungan produksi, pakan, mortalitas, obat, mutasi gudang, **penjualan kasir (nochifram)** dengan nama petugas dan trip | `dash_section_recent_activity` |
| Modal input cepat | `modalProduksi`, `modalPakan`, `modalMortalitas` (+ tipe, nomor baterai), `modalVaksin` | `input_form_*` |

### 4.3 Input cepat mobile — `/input`  `[menu_input]`  ([input/index.blade.php](../resources/views/input/index.blade.php), 1.999 baris)

Pemilih tanggal `[input_filter_tanggal]`, ringkasan stok telur/pakan, populasi kloter, daftar blok yang **sudah/belum** diinput hari itu. Lima form:

| Form `[izin]` | Field (nama input) | Validasi server | Efek samping |
|---|---|---|---|
| **Produksi telur** `[input_form_egg]` → `POST /production/store` | `coop_id` (wajib), `good_eggs` (wajib, ≥0), `broken_eggs` (retak/pecah, ≥0), `abnormal_eggs` (tidak ada di form mobile; ada di modal), `crates_count` (peti, opsional), `weight_kg` (opsional), `date`, `time`, `notes`; "Total Telur" dihitung otomatis | `coop_id` harus ada; angka ≥ 0; **ditolak jika blok+tanggal sudah pernah diinput** ("Data produksi telur untuk … sudah pernah diinput sebelumnya!") | Konversi 10 kg = 1 peti (7.4). Tidak mengubah populasi |
| **Pemakaian pakan** `[input_form_feed]` → `POST /feed/store` | `coop_id`, `feeding_time` (Pagi/Sore), `feed_name` (Layer/Grower/Starter), `quantity_kg` (wajib, ≥0,1), `notes`; ditampilkan populasi aktif, standar pakan master, selisih | **Ditolak jika blok+tanggal+waktu sudah ada** | Otomatis membuat baris `farm_stocks` keluar bercatatan `[AUTO-KONSUMSI]` |
| **Mortalitas / karantina** `[input_form_mortality]` → `POST /mortality/store` | `coop_id`, `count` (wajib, ≥1), `type` (mati / afkir / **sakit** / **sembuh**), `battery_number`, `cause` (Belum diketahui, Sakit, Prolaps, Accident/Cedera, Lainnya), `notes` | `type` harus salah satu dari empat. Opsi sakit/sembuh dan nomor baterai butuh `input_form_quarantine` | mati/afkir → `mortalities` + populasi blok −count. sakit → `quarantines` + populasi −count. sembuh → `quarantines` + populasi +count. Populasi **hanya dikurangi bila cukup**, jika tidak, dikurangi 0 tanpa pesan |
| **Bobot ayam + telur mingguan** `[input_form_weight]` → `POST /weight/store` | `coop_id`, hingga **3 titik sampel**: `sample_N_weight` (kg, ≥0,1), `sample_N_egg` (gram), `sample_N_battery` (default "Titik N"); atau `samples[]`; atau `average_weight_kg` tunggal; `age_weeks`, `notes`. Koma desimal otomatis jadi titik | Minimal 1 bobot, kalau tidak: "Minimal 1 bobot sampel ayam harus diisi!" | Menyimpan 1 baris per titik; **keseragaman %** dihitung (7.8) |
| **Vaksin & obat** `[input_form_health]` → `POST /health/store` | `coop_id` (opsional), kategori obat, `medicine_name` (wajib; pilih dari 26 produk atau ketik nama baru), `dosage` (jumlah), `unit`, `application_method` (default Air minum), `notes`. Menampilkan dosis acuan, indikasi, jadwal, stok terkait | `medicine_name` wajib | Stok obat berkurang otomatis (dihitung dari teks dosis, 7.12). Tipe diisi otomatis dari katalog bila "all" |

Respons JSON (`wantsJson`) atau redirect dengan pesan. Tidak ada upload foto/lokasi (bagian 8).

### 4.4 Gudang — `/gudang`  `[menu_warehouse]`  ([warehouse/*](../resources/views/warehouse))

| Halaman | Isi |
|---|---|
| **Ringkasan** `/gudang` | Tiga kartu stok (telur, pakan, obat) + kartu karantina `[warehouse_card_summary]`; pemilih rentang tanggal `[warehouse_filter_tanggal]`; **8 aliran data** (Telur Masuk, Telur Rusak, Telur Terjual, Pakan Masuk, Pakan Digunakan, Pakan Terjual, Obat Masuk, Obat Digunakan) sebagai tombol pintas `[warehouse_stream_pills]`; **grafik tren 14 hari** `[warehouse_chart_trends]`; tabel mutasi terkini `[warehouse_recent_mutations]`; tombol tambah `[warehouse_btn_add]`; aliran penjualan kasir real-time `[warehouse_sales_stream]` |
| **Telur** `/gudang/telur` `[feature_warehouse_telur]` | Tab: Semua, Masuk, Keluar, Penjualan; cari, filter tanggal; modal input/ubah/detail; kolom peti/kg/butir, sumber, catatan |
| **Pakan** `/gudang/pakan` `[feature_warehouse_pakan]` | Tab Semua/Masuk/Keluar/Penjualan; varian pakan; satuan Kg/Karung; modal edit gudang dan edit pakan kandang; pembagian Layer vs Grower/Starter/Pullet |
| **Obat** `/gudang/obat` `[feature_warehouse_obat]` | Katalog 26 produk dengan stok live (masuk, terpakai, sisa), filter kategori, cari, modal detail (dosis, aplikasi, jadwal, indikasi, catatan), modal input masuk/keluar, estimasi stok setelah transaksi |
| **Karantina** `/gudang/karantina` (alias `/gudang/mortalitas`) `[warehouse_click_quarantine]` | Riwayat ayam sakit/sembuh/mati per blok dan nomor baterai, penyebab, tindakan; modal input/ubah/detail |
| Aksi tulis | `POST /gudang/store`, `PUT /gudang/{id}/update`, `PATCH /gudang/{id}/toggle-status`, `DELETE /gudang/{id}/destroy` — dijaga `feature_warehouse_manage` yang **tidak terdaftar** (3.3 poin b) |

Kategori `farm_stocks`: `telur, pakan, obat, vaksin, vitamin` (+ `disinfektan, mineral` untuk obat). Satuan: Peti, Kg, Karung, Butir, Botol, Box, dll.

### 4.5 Rekap — `/rekap`  `[menu_rekap]`  ([rekap/index.blade.php](../resources/views/rekap/index.blade.php), [rekap/detail.blade.php](../resources/views/rekap/detail.blade.php))
- **Filter**: preset (hari ini, kemarin, 7 hari, 30 hari, bulan ini, bulan lalu) atau rentang tanggal bebas, filter kloter. Default **bulan ini**.
- **Metrik**: total telur (butir, peti, kg), **reject %**, **HDP**, pakan (kg, karung), mortalitas dan %, rata-rata bobot, jumlah vaksin/obat/vitamin.
- **Rekap mingguan M1–M4 per kloter**: rentang tanggal, umur minggu, telur, peti, kg, HDP vs target, reject, pakan, mortalitas.
- **Breakdown per blok**: produksi, HDP, persen kontribusi, pakan realisasi vs standar master, mortalitas.
- **Grafik tren** (Chart.js) dengan pilihan metrik.
- **Tab data**: produksi, pakan, mortalitas, kesehatan.
- **Detail** `/rekap/detail` `[feature_rekap_detail]`: tab produksi, pakan, mortalitas, bobot, vaksin, penjualan; dikelompokkan per tanggal.
- **Kelola data historis** `[feature_rekap_manage]` (default tidak): ubah/hapus telur (`total_eggs`, `broken_eggs`), pakan (`quantity_kg`), mortalitas (`count`, `cause`), bobot (`average_weight_kg`, `uniformity_percentage`), obat (`medicine_name`, `dosage`).
- **Ekspor** `[feature_rekap_export]`: bagian 10.1.

### 4.6 Master — `/master`  `[menu_master]` (default tidak)

| Halaman | Isi | Catatan |
|---|---|---|
| **Hub** `/master` | Kartu menuju semua sub-menu + ringkasan blok; modal edit standar cepat; **modal restore Excel** | |
| **Info Farm** `/master/info-farm` | Nama farm, tagline, **tanggal pullet masuk**, **umur awal pullet (minggu)**, pesan motivasi, pesan kondisi ayam, jadwal tampil, saklar aktif | |
| **Kloter dan Blok** `/master/flocks` | Tambah/ubah/hapus-nonaktifkan kloter (nama, kode, tanggal mulai, umur awal, populasi awal, jenis, catatan) dan blok (kloter, nama, kapasitas, ayam aktif, umur, catatan); **modal panen telur cepat** | Hapus kloter/blok yang punya riwayat → hanya dinonaktifkan. Kode blok **tidak** tersimpan dari form (tidak ada di validasi) |
| **Standar** `/master/standards`, `/standar-produksi`, `/standar-pakan`, `/standar-bb` | Tabel 78 minggu (13–90): HD, berat telur, pakan, bobot min/target/maks, fase, jenis pakan, deskripsi; ubah per minggu; tab *global* untuk 4 parameter | Standar lengkap: 5.3 |
| **Pengaturan** `/master/settings` | `isi_tray`, `berat_telur`, `berat_per_karung`, `hd_target`, `hd_warning`, `hd_minimum`, `reject_maximum` | **Sebagian besar tidak dipakai logika** (5.4) |
| **Obat** `/master/medicines` | Daftar 26 produk (baca saja) | Katalog ada di kode, bukan di DB (5.5) |
| **Hak akses dan pengguna** `/master/hak-akses` | Pilih user, toggle 64 izin, toggle massal per kelompok, tambah user (`name`, `username` alfanumerik/`-`/`_`, `email` opsional, `password` ≥ 4, `role` admin/user), ubah/reset password, aktif/nonaktif | Khusus admin (dicek di dalam fungsi) |
| **Audit** `/master/audit` | Statistik (total aktivitas, hari ini, total terhapus), filter, cari, detail JSON, tombol **Pulihkan** | `[feature_master_audit]`, `[feature_audit_restore]` |
| **Restore Excel** `/master/restore-excel` | Unggah .xlsx/.xls/.csv/.txt ≤ 25 MB, opsi timpa; unduh template contoh | Bagian 10.3 |

### 4.7 Profil — `/profil`  ([profile/index.blade.php](../resources/views/profile/index.blade.php))
Kartu petugas (avatar SVG bawaan, nama, username, peran), modal **Ganti Password** (lama, baru ≥ 6, konfirmasi), modal **Tentang Aplikasi**, tombol keluar. Foto profil tidak bisa diunggah meskipun kolom `avatar` ada.

---

## 5. Data master dan data awal

### 5.1 Apa yang dibuat `php artisan db:seed`
[DatabaseSeeder](../database/seeders/DatabaseSeeder.php) hanya memanggil **`MasterSettingSeeder`** (pengaturan awal, tidak menimpa yang sudah ada) dan **`WeeklyStandardSeeder`** (**menghapus lalu mengisi ulang** semua standar).
**Tidak ada seeder pengguna.** Instalasi baru tidak punya akun; admin harus dibuat manual atau memakai seeder nochifram (`admin` / `password`, tidak aman).
Seeder demo ([FarmSeeder](../database/seeders/FarmSeeder.php), [WarehouseSeeder](../database/seeders/WarehouseSeeder.php), [RekapSeeder](../database/seeders/RekapSeeder.php)) **tidak ikut** `db:seed`, dan bergantung pada user `petugas`/`supervisor` yang tidak dibuat aplikasi ini.

### 5.2 Kloter dan blok (data demo `FarmSeeder`)

| Kloter | Kode | Mulai | Populasi awal → kini | Jenis | Blok (kapasitas / aktif / umur) |
|---|---|---|---|---|---|
| Klotter 1 | K1 | hari ini − 21 minggu | 2.250 → 2.249 | Lohmann Brown | A: 762/762/21 · B: 744/743/21 · C: 744/744/21 |
| Klotter 2 | K2 | hari ini − 21 minggu | 1.770 → 1.768 | Lohmann Brown | D: 750/748/21 · E: 492/492/21 · F: 528/528/21 |

Total 6 blok, ± 4.020 ayam aktif. Pembeli lain akan mengisi sendiri lewat Master → Kloter dan Blok. Ada nilai bawaan **tertanam di kode**: halaman Input memakai 2.249 (K1) dan 1.768 (K2) bila kloter tidak ditemukan.

### 5.3 Standar produksi mingguan (`weekly_standards`)
Sumber: [WeeklyStandardSeeder](../database/seeders/WeeklyStandardSeeder.php), 78 baris (minggu 13–90). Kolom pakan **pagi 40% / sore 60%** dihitung (bukan disimpan). Fase diturunkan dari minggu:

| Minggu | Fase | Pil (`pill`) | Jenis pakan |
|---|---|---|---|
| 13–15 | Grower Akhir (Pra-Laying) | GROWER | Grower / Pullet |
| 16–17 | Persiapan Bertelur (Pre-Lay) | PRE-LAY | Pre-Lay / Layer Awal |
| 18–20 | Awal Bertelur (Puncak Naik) | AWAL BERTELUR | Layer Phase 1 |
| 21–40 | Puncak Produksi (Egg Peak) | PUNCAK PRODUKSI | Layer Phase 1 |
| 41–60 | Laying Phase 2 (Pasca Puncak) | PASCA PUNCAK | Layer Phase 2 |
| 61–90 | Laying Phase 3 (Fase Akhir) / Afkir | FASE AKHIR | Layer Phase 3 |

| Minggu | Fase | HD target (%) | Berat telur (g) | Pakan (g/ekor/hari) | Pagi 40% | Sore 60% | BB min (kg) | BB target (kg) | BB maks (kg) |
|---:|---|---:|---:|---:|---:|---:|---:|---:|---:|
| 13 | GROWER | 0 | - | 75 | 30,0 | 45,0 | 1,11 | 1,14 | 1,18 |
| 14 | GROWER | 0 | - | 77,5 | 31,0 | 46,5 | 1,18 | 1,22 | 1,26 |
| 15 | GROWER | 0 | - | 80 | 32,0 | 48,0 | 1,25 | 1,29 | 1,33 |
| 16 | PRE-LAY | 0 | - | 82,5 | 33,0 | 49,5 | 1,32 | 1,36 | 1,40 |
| 17 | PRE-LAY | 0 | - | 85 | 34,0 | 51,0 | 1,39 | 1,43 | 1,47 |
| 18 | AWAL BERTELUR | 0 | - | 90 | 36,0 | 54,0 | 1,46 | 1,50 | 1,55 |
| 19 | AWAL BERTELUR | 8,5 | 44,6 | 95 | 38,0 | 57,0 | 1,53 | 1,57 | 1,62 |
| 20 | AWAL BERTELUR | 34,5 | 47,1 | 100 | 40,0 | 60,0 | 1,59 | 1,64 | 1,69 |
| 21 | PUNCAK PRODUKSI | 53,5 | 49,7 | 105 | 42,0 | 63,0 | 1,66 | 1,71 | 1,76 |
| 22 | PUNCAK PRODUKSI | 70,6 | 52,2 | 108 | 43,2 | 64,8 | 1,72 | 1,77 | 1,83 |
| 23 | PUNCAK PRODUKSI | 81,5 | 54,4 | 110 | 44,0 | 66,0 | 1,77 | 1,83 | 1,88 |
| 24 | PUNCAK PRODUKSI | 87,3 | 56,3 | 112 | 44,8 | 67,2 | 1,82 | 1,87 | 1,93 |
| 25 | PUNCAK PRODUKSI | 90,5 | 58,1 | 113 | 45,2 | 67,8 | 1,85 | 1,90 | 1,96 |
| 26 | PUNCAK PRODUKSI | 92,3 | 59,3 | 114 | 45,6 | 68,4 | 1,86 | 1,92 | 1,98 |
| 27 | PUNCAK PRODUKSI | 93,4 | 60,5 | 115 | 46,0 | 69,0 | 1,87 | 1,93 | 1,98 |
| 28 | PUNCAK PRODUKSI | 93,9 | 61,3 | 115 | 46,0 | 69,0 | 1,87 | 1,93 | 1,99 |
| 29 | PUNCAK PRODUKSI | 94,3 | 62,0 | 115 | 46,0 | 69,0 | 1,88 | 1,94 | 1,99 |
| 30 | PUNCAK PRODUKSI | 94,5 | 62,6 | 115 | 46,0 | 69,0 | 1,88 | 1,94 | 2,00 |
| 31 | PUNCAK PRODUKSI | 94,7 | 63,2 | 115 | 46,0 | 69,0 | 1,89 | 1,95 | 2,00 |
| 32 | PUNCAK PRODUKSI | 94,8 | 63,6 | 115 | 46,0 | 69,0 | 1,89 | 1,95 | 2,01 |
| 33 | PUNCAK PRODUKSI | 94,9 | 63,9 | 115 | 46,0 | 69,0 | 1,89 | 1,95 | 2,01 |
| 34 | PUNCAK PRODUKSI | 94,8 | 64,2 | 115 | 46,0 | 69,0 | 1,89 | 1,95 | 2,01 |
| 35 | PUNCAK PRODUKSI | 94,7 | 64,4 | 115 | 46,0 | 69,0 | 1,90 | 1,96 | 2,01 |
| 36 | PUNCAK PRODUKSI | 94,6 | 64,6 | 115 | 46,0 | 69,0 | 1,90 | 1,96 | 2,02 |
| 37 | PUNCAK PRODUKSI | 94,4 | 64,8 | 115 | 46,0 | 69,0 | 1,90 | 1,96 | 2,02 |
| 38 | PUNCAK PRODUKSI | 94,2 | 65,0 | 115 | 46,0 | 69,0 | 1,90 | 1,96 | 2,02 |
| 39 | PUNCAK PRODUKSI | 94,1 | 65,1 | 115 | 46,0 | 69,0 | 1,91 | 1,97 | 2,02 |
| 40 | PUNCAK PRODUKSI | 93,9 | 65,3 | 115 | 46,0 | 69,0 | 1,91 | 1,97 | 2,03 |
| 41 | PASCA PUNCAK | 93,7 | 65,4 | 115 | 46,0 | 69,0 | 1,91 | 1,97 | 2,03 |
| 42 | PASCA PUNCAK | 93,5 | 65,6 | 115 | 46,0 | 69,0 | 1,91 | 1,97 | 2,03 |
| 43 | PASCA PUNCAK | 93,3 | 65,7 | 115 | 46,0 | 69,0 | 1,97 ⚠ | 1,97 | 2,03 |
| 44 | PASCA PUNCAK | 93,1 | 65,8 | 115 | 46,0 | 69,0 | 1,92 | 1,98 | 2,04 |
| 45 | PASCA PUNCAK | 92,8 | 65,9 | 115 | 46,0 | 69,0 | 1,92 | 1,98 | 2,04 |
| 46 | PASCA PUNCAK | 92,5 | 66,0 | 115 | 46,0 | 69,0 | 1,92 | 1,98 | 2,04 |
| 47 | PASCA PUNCAK | 92,2 | 66,1 | 115 | 46,0 | 69,0 | 1,92 | 1,98 | 2,04 |
| 48 | PASCA PUNCAK | 91,9 | 66,2 | 115 | 46,0 | 69,0 | 1,93 | 1,99 | 2,04 |
| 49 | PASCA PUNCAK | 91,6 | 66,3 | 115 | 46,0 | 69,0 | 1,93 | 1,99 | 2,05 |
| 50 | PASCA PUNCAK | 91,2 | 66,4 | 115 | 46,0 | 69,0 | 1,93 | 1,99 | 2,05 |
| 51 | PASCA PUNCAK | 90,8 | 66,5 | 115 | 46,0 | 69,0 | 1,93 | 1,99 | 2,05 |
| 52 | PASCA PUNCAK | 90,5 | 66,6 | 115 | 46,0 | 69,0 | 1,99 ⚠ | 1,99 | 2,05 |
| 53 | PASCA PUNCAK | 90,0 | 66,7 | 115 | 46,0 | 69,0 | 1,94 | 2,00 | 2,06 |
| 54 | PASCA PUNCAK | 89,7 | 66,8 | 115 | 46,0 | 69,0 | 1,94 | 2,00 | 2,06 |
| 55 | PASCA PUNCAK | 89,3 | 66,8 | 115 | 46,0 | 69,0 | 1,94 | 2,00 | 2,06 |
| 56 | PASCA PUNCAK | 89,0 | 66,9 | 115 | 46,0 | 69,0 | 1,94 | 2,00 | 2,06 |
| 57 | PASCA PUNCAK | 88,6 | 67,0 | 115 | 46,0 | 69,0 | 1,95 | 2,01 | 2,07 |
| 58 | PASCA PUNCAK | 88,2 | 67,0 | 115 | 46,0 | 69,0 | 1,95 | 2,01 | 2,07 |
| 59 | PASCA PUNCAK | 87,8 | 67,1 | 115 | 46,0 | 69,0 | 1,95 | 2,01 | 2,07 |
| 60 | PASCA PUNCAK | 87,4 | 67,2 | 115 | 46,0 | 69,0 | 1,95 | 2,01 | 2,07 |
| 61 | FASE AKHIR | 87,1 | 67,2 | 116 | 46,4 | 69,6 | 1,95 | 2,01 | 2,07 |
| 62 | FASE AKHIR | 86,7 | 67,3 | 116 | 46,4 | 69,6 | 1,96 | 2,02 | 2,08 |
| 63 | FASE AKHIR | 86,3 | 67,4 | 116 | 46,4 | 69,6 | 1,96 | 2,02 | 2,08 |
| 64 | FASE AKHIR | 85,9 | 67,4 | 116 | 46,4 | 69,6 | 1,96 | 2,02 | 2,08 |
| 65 | FASE AKHIR | 85,6 | 67,5 | 116 | 46,4 | 69,6 | 1,96 | 2,02 | 2,08 |
| 66 | FASE AKHIR | 85,2 | 67,5 | 116 | 46,4 | 69,6 | 1,96 | 2,03 | 2,09 |
| 67 | FASE AKHIR | 84,8 | 67,6 | 116 | 46,4 | 69,6 | 1,97 | 2,03 | 2,09 |
| 68 | FASE AKHIR | 84,4 | 67,7 | 116 | 46,4 | 69,6 | 1,97 | 2,03 | 2,09 |
| 69 | FASE AKHIR | 83,9 | 67,7 | 116 | 46,4 | 69,6 | 1,97 | 2,03 | 2,09 |
| 70 | FASE AKHIR | 83,5 | 67,8 | 116 | 46,4 | 69,6 | 1,97 | 2,03 | 2,09 |
| 71 | FASE AKHIR | 83,0 | 67,8 | 116 | 46,4 | 69,6 | 1,97 | 2,04 | 2,10 |
| 72 | FASE AKHIR | 82,5 | 67,9 | 116 | 46,4 | 69,6 | 1,97 | 2,04 | 2,10 |
| 73 | FASE AKHIR | 81,9 | 67,9 | 117 | 46,8 | 70,2 | 1,98 | 2,04 | 2,10 |
| 74 | FASE AKHIR | 81,4 | 68,0 | 117 | 46,8 | 70,2 | 1,98 | 2,04 | 2,10 |
| 75 | FASE AKHIR | 80,9 | 68,0 | 117 | 46,8 | 70,2 | 1,98 | 2,04 | 2,10 |
| 76 | FASE AKHIR | 80,4 | 68,1 | 117 | 46,8 | 70,2 | 1,98 | 2,04 | 2,10 |
| 77 | FASE AKHIR | 79,8 | 68,1 | 117 | 46,8 | 70,2 | 1,98 | 2,05 | 2,11 |
| 78 | FASE AKHIR | 79,3 | 68,2 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,11 |
| 79 | FASE AKHIR | 78,7 | 68,2 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,11 |
| 80 | FASE AKHIR | 78,2 | 68,3 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,11 |
| 81 | FASE AKHIR | 77,6 | 68,3 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,11 |
| 82 | FASE AKHIR | 77,0 | 68,4 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,11 |
| 83 | FASE AKHIR | 76,4 | 68,4 | 117 | 46,8 | 70,2 | 1,99 | 2,05 | 2,12 |
| 84 | FASE AKHIR | 75,9 | 68,4 | 117 | 46,8 | 70,2 | 1,99 | 2,06 | 2,12 |
| 85 | FASE AKHIR | 75,3 | 68,5 | 117 | 46,8 | 70,2 | 1,99 | 2,06 | 2,12 |
| 86 | FASE AKHIR | 74,7 | 68,5 | 117 | 46,8 | 70,2 | 2,00 | 2,06 | 2,12 |
| 87 | FASE AKHIR | 74,1 | 68,6 | 117 | 46,8 | 70,2 | 2,00 | 2,06 | 2,12 |
| 88 | FASE AKHIR | 73,4 | 68,6 | 117 | 46,8 | 70,2 | 2,00 | 2,06 | 2,12 |
| 89 | FASE AKHIR | 72,8 | 68,6 | 117 | 46,8 | 70,2 | 2,06 ⚠ | 2,06 | 2,12 |
| 90 | FASE AKHIR | 72,2 | 68,7 | 117 | 46,8 | 70,2 | 2,06 ⚠ | 2,06 | 2,12 |

**Catatan data:**
- ⚠ **Minggu 43, 52, 89, 90**: bobot minimum **sama dengan** bobot target (urutannya menunjukkan angka minimum kemungkinan salah ketik, mis. 1,97 padahal minggu 42 = 1,91). Periksa dengan sumber standar breeder `[verifikasi]`.
- HD 0 dan berat telur "-" untuk minggu 13–18 (belum bertelur). HD pertama > 0 di minggu 19 (8,5%).
- Minggu 21 diberi label "Puncak Produksi" padahal HD standarnya baru 53,5%: label hanya pembagian rentang minggu, bukan kondisi sebenarnya.
- Sumber angka (breeder Lohmann Brown atau lain) **tidak dicantumkan** di aplikasi maupun seeder. Pembeli akan menanyakan; cantumkan atau sediakan impor standar breeder.
- Bila minggu di luar 13–90, nilai dijepit ke 13 atau 90. Bila tabel kosong, layanan memakai rumus cadangan (interpolasi) di [ProductionStandardService](../app/Services/ProductionStandardService.php).
- Kloter bisa dibuat ke minggu di luar jangkauan (ayam < 13 minggu / > 90 minggu) → memakai standar minggu terdekat, tanpa peringatan.

### 5.4 Pengaturan (`settings`, tabel key–value, milik nochifram)

Nilai awal dari [MasterSettingSeeder](../database/seeders/MasterSettingSeeder.php). Kolom **Dipakai?** hasil penelusuran kode di luar halaman master.

| Kunci | Nilai awal | Arti | Dipakai logika? |
|---|---|---|---|
| `farm_name` | NOCHI FARM | Nama farm di antarmuka | Ya (tampilan) |
| `farm_tagline` | Peternak Telur Berkualitas | Tagline | Ya (tampilan) |
| `pullet_in_date` | *(default form 2026-04-20)* | Tanggal pullet masuk | Hanya cadangan jika kloter tanpa `start_date` (Rekap) |
| `pullet_initial_age_weeks` | 0 | Umur awal pullet | Tampil di Info Farm |
| `dashboard_motivation_message` | "Semangat bekerja dan tetap jaga kebersihan serta performa kandang hari ini!" | Teks banner | Ya |
| `dashboard_chicken_status_message` | "Kondisi ayam saat ini memasuki umur minggu ke-21 (…)" | Teks kondisi (bila mode manual) | Ya, hanya bila `dashboard_chicken_status_auto` = 0 |
| `dashboard_chicken_status_auto` | *(tidak diseed; default 1)* | 1 = teks kondisi otomatis dari standar | Ya — **tidak ada kontrol di UI** `[verifikasi]` |
| `dashboard_info_schedule_start` / `_end` | 06:00 / 18:00 | Jadwal tampil banner | Tersimpan; pemakaian di tampilan `[verifikasi]` |
| `dashboard_info_active` | 1 | Saklar banner | Ya |
| `isi_tray` | 30 butir | Isi tray/peti untuk **edit rekap** | Ya, hanya `RekapController::updateEggProduction` |
| `berat_telur` | 0,06 kg | Berat telur cadangan | Ya, cadangan bila standar kosong |
| `berat_per_karung` | 50 kg | Berat karung pakan | **Ya, banyak** (pakan, gudang) |
| `default_weight_per_peti` | *(tidak diseed; default 10)* | Kg per peti | Hanya restore Excel; **dashboard memakai angka 10 tertulis di kode** |
| `hd_target` | 95% | Target HD global | **Tidak** — dashboard memakai `hd_target` standar per minggu |
| `hd_warning` | 90% | Ambang peringatan HD | **Tidak dipakai** (hanya form) |
| `hd_minimum` | 88% | HD minimum | **Tidak dipakai** (hanya form) |
| `reject_maximum` | 2% | Batas reject | **Tidak dipakai** (hanya form) |
| `standard_production_egg_crates` | 850 | Target peti harian | **Tidak dipakai** |
| `standard_feed_gram_per_chicken` | 115 | Pakan g/ekor global | **Tidak dipakai** (pakai standar mingguan) |
| `standard_avg_weight_kg` | 1,62 | Bobot rata-rata global | **Tidak dipakai** |
| `standard_weight_tolerance` | 0,05 | Toleransi bobot | **Tidak dipakai** |

> Kesimpulan jujur: **lima parameter di halaman Pengaturan dan empat parameter global di tab Standar tidak memengaruhi perhitungan**. Pemilik yang mengubahnya akan mengira ada efek. Sambungkan atau hapus sebelum dijual.

### 5.5 Katalog obat dan vaksin (26 produk, 8 kategori)
Tertanam di kode [MedicineCatalogService](../app/Services/MedicineCatalogService.php) (bukan tabel DB), jadi **tidak bisa ditambah dari antarmuka**; produk baru hanya dengan mengetik nama saat input.

| Kunci | Label | Contoh produk |
|---|---|---|
| `obat_cacing` | Obat Cacing (Anthelmintik) | Vermixon, Wormzol, Levamisol, Cestocide |
| `antibiotik` | Antibiotik / Antibakteri | Neomeditril, Amoxitin, Doxyvet, Theranest, Koleridin, Trimycin |
| `antikoksidia` | Antikoksidiosis (Obat Berak Darah) | Toltrazuril 2.5%, Coccilin, Amprolin-300 |
| `vitamin` | Vitamin & Suplemen Antistres | Vita Stress, Egg Stimulant, Vitamin B Complex, Fortevit, Vita Chicks |
| `vaksin` | Vaksin (Pencegahan Virus Unggas) | ND Lasota, ND IB, ND Clone G7, Medivac Coryza, AI (Flu Burung), Gumboro |
| `mineral` | Mineral, Kalsium & Premix Layer | Kalsium & Mineral Premix Layer, Egg Shell Booster (CaCO3), DCP |
| `disinfektan` | Disinfektan & Sanitasi Kandang | Medisep, Antisep, Rodalon, Formades, Destan |
| `obat` | Obat Lainnya / Herbal Unggas | Kunyit Ekstrak, Temulawak Unggas, Minyak Ikan, Salep Kaki |

| ID | Produk | Kategori | Satuan | Stok awal demo | Dosis acuan | Cara pemberian |
|---:|---|---|---|---:|---|---|
| 1 | Vermixon | obat_cacing | Botol | 12 | 15–30 ml / 1 L air minum | Air minum |
| 2 | Wormzol-K / Wormzol-B | obat_cacing | Box | 8 | 1 kaplet / 1–2 kg BB atau 1 g / 2 L air | Air minum / Suap langsung |
| 3 | Levamisol (Cestocide / Levavit) | obat_cacing | Gram | 5 | 1 g / 2 L air minum (25–30 mg/kg BB) | Air minum pagi |
| 4 | Neomeditril | antibiotik | Botol | 15 | 0,1 ml / kg BB (0,5–1 ml / 2 L air) | Air minum |
| 5 | Amoxitin | antibiotik | Box | 10 | 1 g / 2 L air minum (10–20 mg/kg BB) | Air minum |
| 6 | Doxyvet | antibiotik | Box | 8 | 1 g / 2 L air minum | Air minum |
| 7 | Theranest / Koleridin | antibiotik | Botol | 7 | 1 g / 1 L air minum | Air minum |
| 8 | Trimycin | antibiotik | Box | 6 | 1 g / 1–2 L air minum | Air minum |
| 9 | Toltrazuril 2.5% (Baycox / Toltracox) | antikoksidia | Botol | 6 | 1 ml / 1 L air minum (25 ppm) | Air minum selama 48 jam berturut-turut |
| 10 | Coccilin / Amprolin-300 | antikoksidia | Box | 8 | 1 g / 1 L air minum | Air minum |
| 11 | Vita Stress | vitamin | Box | 20 | 1 g / 1–2 L air minum | Air minum pagi hari |
| 12 | Egg Stimulant | vitamin | Box | 15 | 1 g / 2 L air minum | Air minum pagi |
| 13 | Vitamin B Complex + Elektrolit | vitamin | Botol | 18 | 1 g / 2 L air minum | Air minum pagi |
| 14 | Fortevit | vitamin | Box | 10 | 1 g / 10 L air minum | Air minum |
| 15 | Vita Chicks | vitamin | Box | 12 | 5 g / 7 L air minum | Air minum |
| 16 | ND Lasota | vaksin | Botol | 6 | 1 botol / 1.000 ekor dosis | Tetes mata / air minum |
| 17 | ND IB Vaccine | vaksin | Botol | 8 | 1.000–2.000 dosis per botol | Tetes mata / air minum |
| 18 | ND Clone 45 / G7 | vaksin | Botol | 5 | 1 botol / 1.000 ekor dosis | Tetes mata / air minum / suntik |
| 19 | Medivac Coryza (Vaksin Snot) | vaksin | Botol | 4 | 0,5 ml per ekor suntik intramuskuler | Suntik paha / dada |
| 20 | Medivac AI (Avian Influenza / Flu Burung) | vaksin | Botol | 4 | 0,5 ml per ekor suntik intramuskuler | Suntik dada / paha |
| 21 | Kalsium & Mineral Premix Layer | mineral | Kg | 25 | 2 kg per 100 kg pakan (2%) | Campur pakan kering |
| 22 | Egg Shell Booster (CaCO3 Murni) | mineral | Kg | 30 | 1–2 kg / 100 kg pakan | Campur pakan sore hari |
| 23 | DCP (Dicalcium Phosphate) | mineral | Kg | 20 | 1 kg / 100 kg pakan | Campur pakan kering |
| 24 | Medisep | disinfektan | Botol | 14 | 15 ml / 10 L air | Semprot kandang, celup kaki (foot dip), cuci peralatan |
| 25 | Antisep | disinfektan | Botol | 10 | 3–5 ml / 10 L air minum, 10 ml / 5 L air semprot | Sanitasi air minum & semprot kandang |
| 26 | Rodalon | disinfektan | Botol | 8 | 15 ml per 10 Liter air | Semprot lingkungan kandang & cuci egg tray |

Setiap produk juga memiliki indikasi, jadwal pemberian, dan catatan (tampil di modal detail). Contoh jadwal vaksin dari katalog: ND IB umur 4/16/24/40 minggu; ND Lasota booster 18–20 minggu lalu tiap 2–3 bulan; Medivac Coryza umur 8 dan 16 minggu; AI umur 16 minggu lalu booster 4–6 bulan.

> ⚠ **Stok awal demo otomatis.** `ensureInitialStock()` memasukkan **26 baris stok masuk fiktif** (4–30 satuan per produk, tanggal 2026-01-01, sumber "Stok Awal Farm") **sekali**, saat halaman yang membaca katalog dibuka dan belum ada satu pun stok obat masuk. Pembeli baru akan melihat stok obat yang tidak pernah ia beli. Lihat temuan 15 poin 11.

### 5.6 Jenis pakan dan opsi pilihan tetap
- Pakan (form): *Pakan Layer, Pakan Grower, Pakan Starter* (tertulis di Blade, bukan master).
- Waktu pemberian: Pagi, Sore.
- Penyebab mortalitas: Belum diketahui, Sakit, Prolaps, Accident/Cedera, Lainnya.
- Status mortalitas: Mati, Afkir, Sakit (karantina), Sembuh.
- Status karantina (gudang): sakit, sembuh, mati.
- Cara aplikasi obat: Air minum (default), tetes mata, suntik, semprot, campur pakan, dll.

### 5.7 Pengguna dan peran
Tidak ada data awal. Dibuat oleh admin di `/master/hak-akses` (atau berasal dari tabel `users` bersama nochifram). Role hanya `admin` dan `user`; **tidak ada "supervisor" sebagai peran** (hanya konsep lewat izin). Email otomatis `username@nochifarm.com` bila dikosongkan.

---

## 6. Kamus data dan relasi tabel

Tipe diambil dari [migration](../database/migrations). Semua tabel memiliki `created_at`, `updated_at` kecuali dicatat. **Tabel bersama** ditandai ◆ (lihat bagian 11).

### 6.1 Tabel milik aplikasi ini

**`flocks`** — kloter (satu angkatan ayam)
| Kolom | Tipe | Fungsi |
|---|---|---|
| id | bigint PK | |
| name | string | Nama kloter ("Klotter 1") |
| code | string null | Kode ("K1"); dibuat otomatis `K{n}` jika kosong |
| start_date | date null | Tanggal ayam masuk |
| initial_age_weeks | uint default 0 | Umur saat masuk (migration `2026_09_14`) |
| initial_population | int | Populasi awal |
| current_population | int | Populasi kini; **hanya dihitung ulang saat blok ditambah/diubah** (bukan oleh mortalitas) |
| breed | string null | Jenis ayam |
| notes | text null | |
| is_active | bool | Aktif |

**`coops`** — blok kandang
| Kolom | Tipe | Fungsi |
|---|---|---|
| id | PK | |
| flock_id | FK → flocks, null on delete set null | Kloter |
| name / code | string | "Blok A" / "A" |
| capacity | int | Kapasitas |
| active_chickens | int | **Populasi aktif (sumber kebenaran harian)** |
| chicken_age_weeks | int | Umur tersimpan; **tampilan memakai umur kloter** (7.2) |
| notes, is_active | | |

**`egg_productions`** — produksi telur harian
| Kolom | Tipe | Fungsi |
|---|---|---|
| flock_id, coop_id, user_id | FK null | Kloter, blok, petugas |
| date, time | date, time | |
| total_eggs | int | Baik + retak/pecah + abnormal |
| good_eggs | int | Telur baik |
| broken_eggs | int | Retak/pecah |
| abnormal_eggs | int default 0 | Telur rusak/abnormal (migration `2026_09_15`) |
| crates_count | decimal(10,2) | Peti (praktis bilangan bulat) |
| weight_kg | decimal(10,2) null | Sisa kg di luar peti penuh |
| notes | text | |

**`feed_consumptions`** — pemakaian pakan
`flock_id, coop_id, user_id, date, time, feeding_time (Pagi/Sore), feed_name (default "Pakan Layer"), quantity_kg decimal(10,2), notes`.

**`mortalities`** — kematian/afkir
`flock_id, coop_id, user_id, date, time, count int default 1, type (mati/afkir/sakit), cause, notes`.

**`weight_samples`** — sampel bobot
| Kolom | Fungsi |
|---|---|
| flock_id, coop_id, user_id, date | Identitas |
| sample_count int default 50 | Jumlah ekor sampel (form baru mengisi 1 per titik) |
| average_weight_kg decimal(6,3) default 1,620 | Bobot titik tersebut (kg) |
| uniformity_percentage decimal(5,2) null | Keseragaman % |
| age_weeks int null | Umur saat ditimbang |
| battery_number string null | Titik/baterai (migration `2026_09_18`) |
| egg_weight_gram decimal(6,2) null | Berat telur sampel |
| **sample_index** tinyint null | Urutan titik 1–3. **Tidak ada di migration; dibuat saat runtime** (15 poin 13) |

**`health_treatments`** — vaksin/obat/vitamin yang diberikan
`flock_id, coop_id, user_id, date, time, type (vaksin/obat/vitamin/disinfektan/… kunci kategori), medicine_name, dosage (teks, mis. "2 Botol"), application_method, notes`.

**`farm_stocks`** — mutasi gudang
`user_id, date, category (telur/pakan/obat/vaksin/vitamin/…), item_name, type (masuk/keluar), quantity decimal(12,2), unit (Kg/Botol/Peti/Item/…), source, notes`. Catatan `[AUTO-KONSUMSI]` menandai cermin otomatis dari input pakan.

**`quarantines`** — karantina
`flock_id, coop_id, user_id, date, time, battery_number (100), count int, status (sakit/sembuh/mati), cause, action_taken, notes`.

**`weekly_standards`** — standar mingguan
`week uint UNIQUE, phase, pill, feed_type, hd_target decimal(5,2), egg_weight string (gram atau "-"), feed_gram decimal(6,2), weight_min/target/max decimal(5,2), description`.

**`user_permissions`**
`user_id FK → users (cascade), permission_key (100), is_enabled bool; UNIQUE (user_id, permission_key)`.

**`audit_logs`** ◆ (tabel juga dibuat nochifram; kolom gabungan)
`user_id, user_name, user_role, action (CREATE/UPDATE/DELETE/RESTORE/LOGIN/LOGOUT), module, description, model_type, model_id, table_name, entity_type, entity_id, entity_code, payload, original_data (longText JSON), changes (longText JSON), ip_address, user_agent, is_restored, restored_at, restored_by, restored_by_name`; indeks `(action,module)`, `created_at`, `is_restored`.

### 6.2 Tabel bersama dari aplikasi lain (◆, tidak dibuat di sini)
| Tabel | Dibuat oleh | Dipakai di sini untuk |
|---|---|---|
| `users` ◆ | nochifram (kolom `role, username, phone, avatar, is_active, permissions` ditambah di sana) | Login, petugas, role |
| `settings` ◆ | nochifram (`key` unik, `value` text) | Semua pengaturan (5.4) |
| `sales`, `sale_items` ◆ | nochifram (kolom `category` telur/pakan, `unit` Peti/Kg/Karung) | Penjualan masuk ke stok telur/pakan dan timeline dashboard |
| `trips` ◆ | nochifram | Nama petugas trip di timeline dashboard |

### 6.3 Diagram relasi (teks)
```
flocks 1───* coops
flocks 1───* egg_productions, feed_consumptions, mortalities, weight_samples,
             health_treatments, quarantines            (flock_id, boleh null)
coops  1───* egg_productions, feed_consumptions, mortalities, weight_samples,
             health_treatments, quarantines            (coop_id, boleh null)
users  1───* semua tabel transaksi di atas + farm_stocks   (user_id = petugas)
users  1───* user_permissions
weekly_standards  ← dibaca lewat minggu umur (coop → flock.current_age_weeks)
farm_stocks       ← stok gudang (telur/pakan/obat), + cermin otomatis konsumsi pakan
sales ─┬─* sale_items  ◆ → dibaca OutboundIntegrationService → stok telur & pakan
audit_logs ← 13 model lewat AuditObserver (snapshot JSON → dasar restore)
```
Penghapusan: FK transaksi `nullOnDelete`, `user_permissions` `cascade`. Menghapus blok/kloter yang punya riwayat hanya **menonaktifkan** (is_active = 0).

---

## 7. Aturan bisnis dan rumus

Semua dikutip dari kode. Rujukan berkas di kolom kanan.

### 7.1 Hen-Day (HD) harian per blok — [DashboardController:411](../app/Http/Controllers/DashboardController.php)
`HD% = round( total_eggs_hari_itu ÷ coop.active_chickens × 100 , 1 )`
`null` ("Belum Input") bila belum ada telur hari itu atau populasi 0. Status **tercapai** bila `HD ≥ hd_target` standar **minggu umur blok** (bukan `hd_target` di Pengaturan).
⚠ Memakai `active_chickens` **saat ini**, bukan populasi pada tanggal tersebut, sehingga HD tanggal lampau bergeser bila populasi berubah.

### 7.2 Umur ayam dinamis — [Flock.php](../app/Models/Flock.php), [ProductionStandardService:175](../app/Services/ProductionStandardService.php)
- Umur kloter sekarang = `initial_age_weeks + selisih_minggu(start_date → sekarang)`; bila `start_date` kosong = `initial_age_weeks`.
- Umur blok pada tanggal acuan = `max(1, umur_kloter_sekarang + floor((tanggal_acuan − hari_ini) / 7))`. Maju ke masa depan menambah umur; mundur mengurangi.
- Blok tanpa kloter memakai `coops.chicken_age_weeks` (cadangan 21).
- Umur dominan farm = minggu dengan **populasi aktif terbanyak** (dipakai untuk pesan kondisi dan berat telur).
- Minggu standar dijepit ke rentang 13–90.

### 7.3 Fase dan standar — [ProductionStandardService:19](../app/Services/ProductionStandardService.php)
Fase, HD target, berat telur (g), pakan (g/ekor/hari), bobot min/target/maks diambil dari `weekly_standards` untuk minggu tersebut (tabel 5.3). Bila tabel kosong → rumus interpolasi cadangan per fase.

### 7.4 Standar pakan — dashboard dan rekap
- Kebutuhan blok per hari (kg) = `active_chickens × gram_pakan(minggu) ÷ 1000`; **pagi 40% / sore 60%**.
- Rekap per blok: jumlah harian selama rentang, tiap hari memakai **umur dinamis hari itu**.
- Karung = `floor(kg ÷ berat_per_karung)`, sisa = `fmod`, default 50 kg.
- Realisasi vs standar: selisih `= realisasi − standar` (merah bila kurang, hijau bila cukup).

### 7.5 Konversi telur: butir, kg, peti — [DashboardController:735](../app/Http/Controllers/DashboardController.php)
- Total telur = `good + broken + abnormal`.
- Bila `crates_count > 0`: pakai. Bila peti 0 **dan** kg diisi: peti 0. Bila **keduanya kosong**: `peti = round(total_telur ÷ 25)`.
- **Aturan 10 kg = 1 peti**: bila `weight_kg ≥ 10`: `peti += floor(kg ÷ 10)`, `kg = round(kg − 10×extra, 1)`; kg ≤ 0 → null. Peti selalu bulat.
- Tes `test_ten_kg_equals_one_crate_rule_and_comparison` membuktikan 8 peti + 10 kg → 9 peti, 0 kg.

### 7.6 Perbandingan realisasi vs perkiraan (deteksi selisih) — [dashboard.blade.php:817](../resources/views/dashboard.blade.php)
- **Perkiraan** `kg = round(total_eggs_blok × berat_telur_standar(g) ÷ 1000, 1)` (60 g bila standar kosong); `peti = floor(kg ÷ 10)`.
- **Realisasi** `kg = peti×10 + kg` (hasil timbang yang diinput petugas).
- **Sesuai** bila `realisasi ≥ perkiraan` dan perkiraan > 0; selain itu "Kurang X kg". Selisih positif tampil "Lebih +X kg".
- Tujuan: jika petugas melaporkan butir tetapi hasil timbang kurang dari estimasi, ditandai merah.
- ⚠ Perkiraan memakai `total_eggs` (termasuk retak/pecah), bukan telur baik, sehingga bisa lebih tinggi dari kenyataan bersih.

### 7.7 HDP, reject, mortalitas pada Rekap — [RekapController:131](../app/Http/Controllers/RekapController.php)
- **HDP rentang** `= round( total_butir ÷ (populasi_aktif_sekarang × jumlah_hari) × 100 , 1 )`. HDP mingguan kloter = `butir ÷ (populasi_aktif_kloter × 7) × 100`.
- **Reject %** `= round( broken_eggs ÷ total_eggs × 100 , 1)`; hanya `broken_eggs` (retak/pecah), **tidak** menghitung `abnormal_eggs`.
- **Mortalitas %** `= round( Σ mortalities.count ÷ populasi_aktif_sekarang × 100 , 2)`. Kematian di karantina **tidak** dihitung (7.10).
- **Nilai tampil bila data kosong (tertulis di kode, bukan hasil hitung):** reject 1,2% · mortalitas 2,09% (rentang) / 0,50% (mingguan) · bobot rata-rata 1,60 kg · fase pakan "Grower" untuk M1, "Layer" untuk M2–M4. Lihat temuan 15 poin 9.

### 7.8 Bobot badan dan keseragaman — [DashboardController:1098](../app/Http/Controllers/DashboardController.php)
- Sampel 1–3 titik per blok per hari, masing-masing bobot (kg) dan opsional berat telur (g).
- **Keseragaman %** = persen sampel yang berada dalam **±10% dari rata-rata** sampel hari itu; hanya dihitung bila ≥ 2 sampel dan rata-rata > 0, `round(…,1)`.
- Tampilan dashboard: titik **"Capai"** bila bobot ≥ `bb_target` minggu umur; telur dinilai terhadap toleransi berat telur standar `[verifikasi]`.

### 7.9 Populasi — [DashboardController:874](../app/Http/Controllers/DashboardController.php), [WarehouseController:1440](../app/Http/Controllers/WarehouseController.php)
| Kejadian | `coops.active_chickens` | `flocks.current_population` |
|---|---|---|
| Mati / afkir | −count **bila `aktif ≥ count`**, jika tidak tidak berubah | tidak berubah |
| Sakit | −count (idem syarat) | tidak berubah |
| Sembuh | +count | tidak berubah |
| Karantina "mati" (lewat Gudang) | tidak berubah (sudah dikurangi saat sakit) | tidak berubah |
| Ubah mortalitas di Rekap | **tidak disesuaikan** | tidak berubah |
| Hapus mortalitas di Rekap | **tidak dikembalikan** | tidak berubah |
| Pulihkan mortalitas terhapus (Audit) | −count **lagi** | tidak berubah |
| Tambah/ubah blok (Master) | nilai diketik admin | **dihitung ulang** = Σ aktif blok |
| Restore Excel | hanya bila blok kosong (dari kolom Populasi) | tidak berubah |

⚠ Akibat: populasi bisa **melenceng** dan HD/mortalitas ikut salah. Contoh: hapus catatan mati 10 ekor (populasi tidak naik) lalu pulihkan dari Audit (populasi turun 10 lagi) → populasi berkurang 10 ekor ekstra.

### 7.10 Karantina — [Quarantine.php](../app/Models/Quarantine.php)
`ayam_dikarantina = max(0, Σsakit − Σsembuh − Σmati)` (semua riwayat, tanpa filter tanggal).
⚠ Kematian di karantina hanya ada di `quarantines`, **tidak membuat baris `mortalities`**: kartu/rekap mortalitas (hanya `mortalities`) tidak menghitungnya.

### 7.11 Stok telur — [OutboundIntegrationService:20](../app/Services/OutboundIntegrationService.php)
- **Masuk** = Σ produksi (`crates_count` peti, `weight_kg` kg) + `farm_stocks` telur masuk (Peti, Kg).
- **Keluar** = penjualan nochifram (kategori telur: Σ unit Peti, Σ unit Kg) + telur rusak (`farm_stocks` keluar Peti/Kg + `broken_eggs × berat_telur_target_kg` + `farm_stocks` keluar Butir × berat target).
- Setiap sisi dinormalisasi 10 kg = 1 peti (bulat); **stok = (masuk_peti×10 + masuk_kg) − (keluar_peti×10 + keluar_kg)**, bisa **negatif** (ditampilkan bertanda minus).
- Berat telur target = standar minggu umur pada tanggal produksi (g ÷ 1000); cadangan `berat_telur` lalu 0,06.

### 7.12 Stok pakan — [OutboundIntegrationService:171](../app/Services/OutboundIntegrationService.php)
- **Beli** = Σ `farm_stocks` pakan masuk, satuan dikonversi ke kg: karung/sak/krg × `berat_per_karung`, ton × 1.000, lainnya apa adanya.
- **Keluar** = konsumsi kandang (Σ `feed_consumptions.quantity_kg`) + penjualan (karung × berat + kg) + keluar manual **selain** yang bercatatan `[AUTO-KONSUMSI]` (agar tak dobel).
- **Stok = Beli − Keluar** (kg dan karung, 1 desimal).
- Pembagian **Layer vs Grower/Starter/Pullet** hanya dari **kata kunci pada nama** (`grower`, `starter`, `pullet`); nama berbeda salah klasifikasi.

### 7.13 Stok obat — [MedicineCatalogService](../app/Services/MedicineCatalogService.php)
`sisa = Σ farm_stocks masuk (obat/vaksin/vitamin/…) − Σ health_treatments (jumlah dari teks dosis)`.
Jumlah diambil dari **teks bebas** `dosage` oleh `parseDosageQuantity`: pola "2 Botol", "3 Box", "1,5 Liter", "2 (2000 dosis)", "1000 dosis" (≥ 500 dosis → ÷ 1000), lalu angka pertama < 100; selain itu **1**. Rapuh: dosis ditulis tidak lazim → terhitung 1.

### 7.14 Validasi input ganda
| Data | Aturan |
|---|---|
| Produksi telur | Satu per **blok + tanggal** |
| Pakan | Satu per **blok + tanggal + waktu (Pagi/Sore, tak peka huruf besar)** |
| Mortalitas, bobot, obat, gudang | **Tidak ada** pengecekan duplikat |
| Restore Excel | Telur: satu per blok+tanggal (timpa/lewati); pakan: blok+tanggal+waktu; mortalitas mati: blok+tanggal+tipe |

### 7.15 Aturan edit data historis (Rekap) — [RekapController:852](../app/Http/Controllers/RekapController.php)
- **Telur**: input `total_eggs`, `broken_eggs`; `good = total − broken` (error bila negatif; `abnormal_eggs` diabaikan); lalu **menimpa** `crates_count = round(total ÷ isi_tray, 1)` dan `weight_kg = round(total × berat_telur, 2)` (`isi_tray` default 30, `berat_telur` default 0,06).
  ⚠ Hasil timbang asli petugas **hilang** diganti estimasi, dan memakai konstanta "30" (bertentangan dengan 10 kg/peti dan 25 butir/peti, lihat 7.16).
- **Pakan**: `quantity_kg` ≥ 0 (tidak memperbarui cermin `farm_stocks`; tidak masalah karena cermin dikecualikan dari hitungan stok).
- **Mortalitas**: `count` ≥ 1, `cause`; **populasi tidak disesuaikan** (7.9).
- **Bobot**: `average_weight_kg` ≥ 0,1; `uniformity_percentage` 0–100. **Obat**: `medicine_name`, `dosage` wajib.

### 7.16 ⚠ Konstanta "peti" yang tidak konsisten
Satu kata "peti" dipakai dengan empat definisi di kode:
| Definisi | Dipakai di |
|---|---|
| **10 kg = 1 peti** | Input, dashboard, gudang, integrasi, restore (default `default_weight_per_peti` = 10) |
| **25 butir = 1 peti** | Estimasi peti saat input kosong (`/25`), gudang update telur, telur rusak peti→butir (`×25`) |
| **250 butir = 1 peti** | Rekap (`×250`), tampilan Master panen telur (`/250`) |
| **30 butir = 1 tray/peti** | Pengaturan `isi_tray`, edit data Rekap (`/30`) |
| Kg → butir `× 16` | Rekap (penjualan kg → butir) |

Sebagai gambaran, 10 kg telur 60 g ≈ 167 butir, jadi 25/30/250 saling bertentangan. **Tentukan satu definisi peti** (dan pusatkan di satu fungsi) sebelum dijual; ini sumber angka tidak cocok antar halaman.

---

## 8. Lokasi, GPS, foto, peta

| Fitur | Status |
|---|---|
| GPS / geolokasi (`latitude`, `longitude`, `navigator.geolocation`) | **Tidak ada** |
| Peta (Leaflet, Google Maps, dll.) | **Tidak ada** |
| Unggah foto (nota, kandang, ayam, bukti panen) | **Tidak ada** (satu-satunya input file adalah unggah Excel di Master) |
| Kamera / pemindai barcode/QR | **Tidak ada** |
| Foto profil | **Tidak ada** (kolom `avatar` ada, tidak ada fiturnya) |
| Cap lokasi pada input harian | **Tidak ada**; bukti hanya nama petugas, waktu, IP/user agent di audit |

Fitur GPS dan foto bukti **ada di aplikasi nochifram** (penjualan), bukan di aplikasi ini. Jika calon pembeli menanyakan, jawab jelas: aplikasi input tidak memiliki GPS/foto. Menambahkan foto bukti timbang dan lokasi pada input telur/pakan adalah peluang nilai jual.

---

## 9. Daftar lengkap 64 izin

"Default non-admin" = berlaku bila admin belum mengatur izin user tersebut. Admin selalu memiliki semua izin. Sumber: [PermissionService](../app/Services/PermissionService.php), diekspor langsung dari registri.

**1. Navigator Menu Utama (Navbar & Bottom Bar)** (`menu`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 1 | `menu_dashboard` | Menu Dashboard | ya | Menampilkan link navigasi dan halaman Dashboard ringkasan |
| 2 | `menu_warehouse` | Menu Gudang | ya | Menampilkan link navigasi dan halaman Gudang |
| 3 | `menu_input` | Menu Input Cepat Mobile | ya | Menampilkan link navigasi tombol (+) dan halaman Input Mobile |
| 4 | `menu_rekap` | Menu Rekap & Laporan | ya | Menampilkan link navigasi dan halaman Rekapitulasi Data |
| 5 | `menu_master` | Menu Master Data | tidak | Menampilkan link navigasi dan halaman Master Konfigurasi Farm |

**2. Dashboard: Filter & Kartu Ringkasan Hari Ini** (`dashboard_cards`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 6 | `dash_filter` | Filter Klotter & Pemilih Tanggal | ya | Widget pemilih klotter aktif di dashboard |
| 7 | `dash_filter_tanggal` | Pemilih Tanggal Dashboard | ya | Akses untuk mengubah tanggal (Pilih Tanggal) di dashboard |
| 8 | `dash_card_flock` | Kartu Kloter & Populasi Ayam | ya | Menampilkan metrik total kloter, total blok, populasi ayam aktif, umur, dan persentase populasi per kloter |
| 9 | `dash_card_egg` | Kartu Produksi Telur Hari Ini | ya | Menampilkan metrik panen telur (Peti & Butir) hari ini |
| 10 | `dash_card_feed` | Kartu Pemakaian Pakan Hari Ini | ya | Menampilkan metrik pemakaian pakan (Kg & Jenis Pakan) hari ini |
| 11 | `dash_card_mortality` | Kartu Mortalitas Ayam Hari Ini | ya | Menampilkan metrik jumlah ayam mati/afkir harian |
| 12 | `dash_card_mortality_click` | Klik Kartu Mortalitas (Ke Gudang Mortalitas & Karantina) | tidak | Akses untuk mengklik kartu Mortalitas di dashboard untuk diarahkan ke halaman riwayat Mortalitas & Karantina |
| 13 | `dash_card_weight` | Kartu Bobot Badan Ayam | ya | Menampilkan ringkasan sampel bobot badan 6 blok di dashboard |
| 14 | `dash_card_weight_click` | Klik Kartu Bobot Ayam (Evaluasi Master 6 Blok) | tidak | Izin untuk mengklik kartu Bobot Ayam di dashboard untuk membuka modal evaluasi perbandingan data sampel vs master 6 blok |
| 15 | `dash_card_health` | Kartu Vaksin & Obat Hari Ini | ya | Menampilkan ringkasan kegiatan perlakuan medis hari ini |
| 16 | `dash_card_quarantine` | Kartu Karantina Ayam Hari Ini | tidak | Menampilkan metrik ayam sakit dalam masa karantina/pemulihan di dashboard |
| 17 | `dash_card_quarantine_click` | Klik Kartu Karantina Ayam (Ke Gudang Karantina) | tidak | Akses untuk mengklik kartu Karantina Ayam di dashboard untuk diarahkan ke halaman riwayat Gudang Karantina |

**3. Dashboard: Status Blok Kandang & Analisa HD** (`dashboard_coops`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 18 | `dash_section_coops` | Section Status Blok Kandang Aktif | ya | Menampilkan kontainer daftar kartu seluruh blok kandang (Blok A–F) |
| 19 | `dash_coop_hd` | Badge & Angka Hen-Day (HD) Blok | ya | Menampilkan persentase performa HD dan status fase umur per blok |
| 20 | `dash_coop_standards` | Acuan Telur & Standar Pakan Blok | ya | Menampilkan target berat telur dan standar gram pakan per ekor |
| 21 | `dash_coop_egg_comparison` | Realisasi Panen Telur vs Perkiraan | ya | Kotak perbandingan hitungan aplikasi vs input karyawan (Merah/Hijau) |
| 22 | `dash_coop_feed_comparison` | Standar Pakan vs Realisasi Input Pakan | ya | Kotak hitungan kebutuhan pakan harian blok vs realisasi pakan yang diinput |
| 23 | `dash_coop_fase_info` | Penjelasan Alasan Fase Umur Ayam | ya | Teks keterangan fase (Puncak Produksi, Grower, dll) dengan tombol detail |

**4. Dashboard: Kolom Gudang & Timeline Aktivitas** (`dashboard_sidebar`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 24 | `dash_section_warehouse_summary` | Section Gudang & Integrasi Penjualan | ya | Kontainer ringkasan stok telur & pakan di kolom kanan desktop |
| 25 | `dash_widget_egg_stock` | Widget Stok Telur Saat Ini | ya | Menampilkan angka stok telur terkini, telur masuk, dan telur keluar/terjual |
| 26 | `dash_widget_feed_stock` | Widget Stok Pakan Saat Ini | ya | Menampilkan angka stok pakan terkini, konsumsi kandang, dan pakan terjual |
| 27 | `dash_section_recent_activity` | Timeline Aktivitas Terakhir | ya | Menampilkan log pergerakan real-time: produksi, pakan, obat, mutasi & penjualan kasir |

**6. Gudang: Ringkasan Stok & Tab Kategori** (`warehouse_summary`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 28 | `warehouse_card_summary` | Kartu Ringkasan Stok Barang | ya | Tiga kartu ringkasan stok telur, pakan, dan obat di bagian atas halaman gudang |
| 29 | `warehouse_filter_tanggal` | Pemilih Tanggal Gudang | ya | Akses untuk memilih rentang tanggal di halaman Gudang |
| 30 | `warehouse_click_telur` | Klik Kartu Gudang Telur | ya | Akses untuk mengklik kartu Gudang Telur menuju detail |
| 31 | `warehouse_click_pakan` | Klik Kartu Gudang Pakan | ya | Akses untuk mengklik kartu Gudang Pakan menuju detail |
| 32 | `warehouse_click_obat` | Klik Kartu Gudang Obat | ya | Akses untuk mengklik kartu Gudang Obat menuju detail |
| 33 | `warehouse_click_quarantine` | Klik Kartu Ayam Karantina | tidak | Akses untuk mengklik kartu Ayam Karantina menuju pencatatan & detail karantina |
| 34 | `feature_warehouse_telur` | Lihat Gudang Telur | ya | Membuka tab dan detail data stok serta mutasi telur utuh/rusak |
| 35 | `feature_warehouse_pakan` | Lihat Gudang Pakan | ya | Membuka tab dan detail data stok pakan masuk/keluar |
| 36 | `feature_warehouse_obat` | Lihat Gudang Obat & Vaksin | ya | Membuka tab dan detail data stok obat dan suplemen |
| 37 | `feature_warehouse_karantina` | Lihat Gudang Ayam Karantina | tidak | Membuka sub-halaman dan manajemen data riwayat ayam karantina (sakit, sembuh, mati) |
| 38 | `warehouse_card_quarantine` | Kartu Ayam Karantina di Gudang | tidak | Menampilkan kartu ringkasan populasi ayam karantina (masuk sakit, keluar sembuh, sisa diisolasi) di gudang |

**7. Gudang: Tren Aliran & 8 Data Stream** (`warehouse_streams`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 39 | `warehouse_chart_trends` | Tren Semua Aliran Barang (Grafik) | ya | Grafik visualisasi pergerakan 14 hari (Masuk, Digunakan, Keluar, Terjual) |
| 40 | `warehouse_stream_pills` | Akses Langsung 8 Aliran Data Gudang | ya | Tombol-tombol pill pintas (Telur Masuk, Rusak, Terjual, Pakan Masuk, dll) |

**8. Gudang: Mutasi & Penjualan Kasir** (`warehouse_mutations`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 41 | `warehouse_recent_mutations` | Tabel Mutasi Terkini Gudang | ya | Menampilkan riwayat catatan pergerakan barang masuk & keluar farm |
| 42 | `warehouse_btn_add` | Tombol Tambah Mutasi Barang | ya | Tombol untuk mencatat mutasi stok masuk/keluar baru |
| 43 | `warehouse_btn_manage` | Aksi Edit & Hapus Mutasi Gudang | ya | Tombol Edit dan Hapus pada setiap baris data mutasi gudang |
| 44 | `warehouse_sales_stream` | Barang Keluar: Penjualan Real-Time Kasir | ya | Menampilkan daftar penjualan yang otomatis terhubung ke sistem kasir nochifram |

**9. Modul Input Transaksi (Mobile)** (`input_forms`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 45 | `input_filter_tanggal` | Ubah Tanggal Input | ya | Akses untuk mengubah tanggal transaksi pada halaman Input |
| 46 | `input_form_egg` | Pilihan Transaksi: Produksi Telur | ya | Kartu dan formulir input panen telur di halaman input mobile |
| 47 | `input_form_feed` | Pilihan Transaksi: Pemakaian Pakan | ya | Kartu dan formulir input konsumsi pakan di halaman input mobile |
| 48 | `input_form_mortality` | Pilihan Transaksi: Mortalitas Ayam | ya | Kartu dan formulir input kematian/afkir di halaman input mobile |
| 49 | `input_form_weight` | Pilihan Transaksi: Bobot Ayam | ya | Kartu dan formulir input bobot timbangan di halaman input mobile |
| 50 | `input_form_health` | Pilihan Transaksi: Vaksin & Obat | ya | Kartu dan formulir input perlakuan obat di halaman input mobile |
| 51 | `input_form_quarantine` | Pilihan Status Karantina (Sakit/Sembuh) & No. Baterai | tidak | Mengaktifkan opsi status Sakit dan Sembuh serta input nomor baterai pada formulir mortalitas |

**10. Modul Rekap Data & Laporan** (`rekap`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 52 | `rekap_filter` | Filter Blok Rekap | ya | Widget filter kandang di modul rekap |
| 53 | `rekap_filter_tanggal` | Filter Tanggal Rekap | ya | Akses untuk mengubah rentang tanggal di halaman Rekap |
| 54 | `feature_rekap_view` | Tabel Rekapitulasi Data Harian | ya | Menampilkan tabel rekap produksi, pakan, dan mortalitas harian & bulanan |
| 55 | `feature_rekap_detail` | Halaman Detail Rekap Kandang | ya | Membuka halaman analisa detail dan grafik performa per blok |
| 56 | `feature_rekap_export` | Tombol Export Laporan Excel | ya | Tombol untuk mengunduh laporan rekapitulasi format .xlsx |
| 57 | `feature_rekap_manage` | Edit & Hapus Data Historis Rekap | tidak | Tombol koreksi dan penghapusan data produksi/pakan lama pada tabel rekap |

**11. Modul Master Data & Konfigurasi** (`master`)

| No | Kunci izin | Label | Default non-admin | Fungsi |
|---:|---|---|:--:|---|
| 58 | `feature_master_info_farm` | Informasi Profil Peternakan | tidak | Melihat dan mengubah profil informasi peternakan |
| 59 | `feature_master_coops` | Master Flock & Blok Kandang | tidak | Tambah, edit, dan hapus data klotter, blok, kapasitas, dan populasi ayam |
| 60 | `feature_master_standards` | Standar Produksi, Pakan & Bobot | tidak | Konfigurasi acuan standar mingguan umur 13–90 minggu |
| 61 | `feature_master_settings` | Pengaturan Sistem & Obat | tidak | Pengaturan parameter sistem farm, bobot karung, dan daftar obat |
| 62 | `feature_master_permissions` | Manajemen Hak Akses & Pengguna | tidak | Pengaturan toggle izin seluruh pengguna (Khusus Super Admin) |
| 63 | `feature_master_audit` | Menu Audit Riwayat & Log Aktivitas | tidak | Melihat dan memantau riwayat aktivitas transaksi serta perubahan data seluruh pengguna |
| 64 | `feature_audit_restore` | Restore Data Terhapus (Pulihkan Data) | tidak | Izin khusus untuk memulihkan / me-restore data yang telah dihapus kembali ke database (Admin / Supervisor) |

**Kunci dipakai di route/kode tetapi tidak ada di registri:** `feature_warehouse_manage` (3.3 b).
**Kunci di registri tetapi tidak dipakai:** `feature_master_info_farm`, `feature_master_coops`, `feature_master_standards`, `feature_master_settings`, `feature_master_permissions`, `feature_rekap_view`, `warehouse_btn_add` (3.3 c).
Izin berisiko yang **default ya**: `input_filter_tanggal` (mengubah tanggal input), `warehouse_btn_manage` (tampilan saja), `feature_warehouse_telur/pakan/obat`.

---

## 10. Ekspor, cetak, impor, audit, restore

### 10.1 Ekspor rekap — `GET /rekap/export-excel?tab=…`  `[feature_rekap_export]`
- **Formatnya CSV UTF-8 dengan BOM**, bukan `.xlsx` (nama `rekap_{tab}_{mulai}_{akhir}.csv`; tombol berlabel "Export Excel"). Excel membukanya dengan benar.
- Tab dan kolom:

| Tab | Kolom |
|---|---|
| produksi | Tanggal, Produksi Telur (Peti), Produksi Telur (Butir), Telur Retak (Butir), Telur Utuh (Butir) + baris Total |
| pakan | Tanggal, Waktu, Waktu Pakan, Nama Pakan, Jumlah (Kg), Kandang, Catatan + Total |
| mortalitas | Tanggal, Waktu, Kematian (Ekor), Penyebab, Kandang, Catatan + Total |
| bobot | Tanggal, Bobot Rata-rata (Kg), Jumlah Sampel (Ekor), Keseragaman (%), Umur (Minggu), Catatan |
| vaksin | Tanggal, Waktu, Jenis, Nama Obat / Vaksin, Dosis, Metode, Catatan |
| penjualan | Tanggal, No Invoice, Kategori, Nama Item, Kuantitas, Satuan, Harga Satuan (Rp), Total (Rp), Nama Pelanggan, Metode Bayar, Status + Total (data nochifram) |

### 10.2 Cetak / PDF
**Tidak ada** cetak laporan dan PDF di aplikasi ini (tidak ada paket DomPDF; nochifram memilikinya). Pembeli yang butuh laporan siap cetak harus ditambah.

### 10.3 Impor / restore dari Excel (format **NF-DAT-002**) — `POST /master/restore-excel`
- Berkas `.xlsx`, `.xls`, `.csv`, `.txt` maks **25 MB**; pembacaan memakai PhpSpreadsheet bila ada, atau pembaca XML/ZIP bawaan.
- Template contoh: `GET /master/restore-excel/template`. Kolom (indeks 0 = A):

| Kol | Isi | | Kol | Isi |
|---|---|---|---|---|
| A | ID | | O | Reject % |
| B | Hari | | P | Petugas |
| C | **Tanggal** | | Q | Catatan |
| D | Mode (kloter) | | R | Jam Input |
| E | Minggu | | S | Status |
| F | **Umur** (mis. "18 A") | | T | **Pakan Pagi** (kg) — *dipaksa kolom T* |
| G | **Blok** | | U | **Pakan Sore** (kg) — *dipaksa kolom U* |
| H | Populasi | | V | Total Pakan |
| I | **Baik** | | W | Target Pakan Pagi |
| J | **Retak** | | X | Target Pakan Sore |
| K | **Pecah** | | Y | Target Pakan Total |
| L | Kotor | | Z | **Ayam Mati** |
| M | **Total** | | AA | Ayam Afkir |
| N | HD % | | AB | Total Mati |
| | | | AC | Harga |

Yang **benar-benar diimpor**: tanggal, blok, populasi (hanya bila blok masih kosong), baik, retak, pecah, total, petugas (dicari lewat nama/username), catatan, jam, pakan pagi (T), pakan sore (U), ayam mati (Z). **Afkir (AA), kotor (L), harga (AC), HD%, dan target pakan tidak diimpor** `[verifikasi]`.
Pemetaan telur: `retak → abnormal_eggs`, `pecah → broken_eggs`; berat dan peti **diestimasi** = `(baik > 0 ? baik : total) × berat telur standar(g) ÷ 1000`, `peti = floor(kg ÷ default_weight_per_peti)`.
Aturan: baris tanpa tanggal valid atau blok tidak dikenali **dilewati diam-diam**; blok dicocokkan lewat huruf A–F; mode timpa memperbarui data yang sudah ada; seluruh baris dalam **satu transaksi** (rollback bila gagal); hasil ringkas (baris diproses, telur/pakan/mortalitas dibuat atau diperbarui, blok dan tanggal terdampak) dan dicatat di audit.
⚠ Sebelum impor, `cleanupDuplicateFlocksAndCoops()` **menghapus** kloter bernama "Kloter 1/Klotter 1" duplikat beserta blok dan datanya (menggabung ke yang ber-ID terkecil), di luar transaksi dan dengan error ditelan. Terikat nama "Kloter 1" dan blok A–C: tidak berlaku umum.

### 10.4 Audit log dan restore
- **Direkam otomatis** (Observer): create, update (nilai lama dan baru), delete (snapshot lengkap) untuk 13 model: `EggProduction, FeedConsumption, Mortality, Quarantine, WeightSample, HealthTreatment, FarmStock, Coop, Flock, User, Setting, WeeklyStandard, UserPermission`; plus LOGIN/LOGOUT. Mencatat pengguna (nama, role), IP, user agent, waktu, deskripsi bahasa Indonesia.
- **Modul** di filter: telur, pakan, mortalitas, karantina, bobot, obat, gudang, master, pengguna, auth.
- **Pulihkan** (`POST /master/audit/{id}/restore`, `[feature_audit_restore]`): hanya untuk catatan **DELETE**, sekali saja (`is_restored`, siapa, kapan). Membuat ulang baris dari snapshot (mempertahankan ID bila bebas). Bila blok asal sudah tidak ada, **dialihkan diam-diam ke blok aktif pertama**. Untuk mortalitas, populasi blok dikurangi lagi (lihat 7.9).
- ⚠ **Kata sandi (hash) ikut tersimpan di audit** untuk model `User` (snapshot create/delete, nilai lama/baru saat update) dan tampil di detail JSON audit. Hapus atribut sensitif sebelum dicatat.
- ⚠ Tabel `audit_logs` dibuat juga oleh nochifram dengan kolom `entity_type` wajib; log aplikasi mencatat **83 kegagalan** "Field 'entity_type' doesn't have a default value" (54 `local`, 29 `testing`). Kode kini mengirim `entity_type`, tetapi semua pencatatan lama yang gagal **tidak tercatat** `[verifikasi]` kini sudah tidak terjadi.

---

## 11. Integrasi dan tabel bersama

| Arah | Mekanisme | Isi |
|---|---|---|
| nochifram → input | Baca langsung tabel `sales`, `sale_items`, `trips` (query `DB::table`, model `Sale`, `SaleItem`) | Penjualan telur dan pakan dikurangkan dari stok; muncul di timeline dashboard dengan nama petugas trip dan di tab rekap "penjualan" |
| input → company profile | companynochifarm membaca `coops`, `flocks`, `egg_productions`, `health_treatments` | Populasi dan aktivitas di situs publik |
| input → pengeluaran | nochifarmpengeluaran membaca `egg_productions`, `coops`, `flocks` | Halaman produksi di aplikasi keuangan |
| Bersama | `users`, `settings`, `audit_logs` | Satu akun dan satu pengaturan untuk semua aplikasi |

Konsekuensi: aplikasi ini **tidak bisa dipasang sendirian** (5.4 dan 6.2): login dan pengaturan membutuhkan tabel/kolom dari nochifram. Penjualan hanya muncul bila nochifram dipasang. Untuk dijual mandiri perlu: migration `users` lengkap, migration `settings`, dan mode "tanpa modul penjualan" (query `sales` dibungkus agar tidak error bila tabel tidak ada `[verifikasi]`).

---

## 12. Alur kerja per peran dan skenario demo

### 12.1 Petugas kandang (harian, lewat HP)
1. Buka aplikasi, login dengan username dan password.
2. Lihat **Dashboard**: blok mana yang "Belum Input", kebutuhan pakan hari ini, kondisi/fase ayam.
3. Menu **Input (+)** → pilih tanggal (default hari ini).
4. **Pagi**: *Pakan* — pilih blok, waktu Pagi, jenis, isi kg aktual (lihat standar pakan dan selisih) → simpan.
5. **Panen telur** — pilih blok, isi telur baik dan retak/pecah, isi peti dan kg hasil timbang → simpan. Dashboard menandai hijau bila hasil timbang ≥ perkiraan.
6. **Sore**: *Pakan* waktu Sore.
7. Bila ada ayam mati/afkir → *Mortalitas* (jumlah, penyebab). Ayam sakit → status **Sakit** (masuk karantina, nomor baterai). Ayam pulih → **Sembuh**.
8. Mingguan → *Timbang ayam + telur* (hingga 3 titik).
9. Saat vaksin/obat → *Vaksin & Obat* (pilih produk, jumlah, cara).
10. Cek dashboard: HD blok hijau/merah dibanding target umur.
11. Jika salah input dan tidak berizin edit → lapor admin.

### 12.2 Admin / pemilik
1. **Dashboard**: HD tiap blok, realisasi vs perkiraan telur/pakan, stok telur dan pakan, penjualan hari ini.
2. **Rekap**: pilih periode (mis. bulan lalu) → HDP, reject, pakan, mortalitas; M1–M4 per kloter; unduh CSV.
3. **Gudang**: catat pembelian pakan (masuk), restok obat, mutasi telur manual, lihat 8 aliran dan grafik 14 hari.
4. **Master**: tambah kloter dan blok saat ayam baru masuk (isi tanggal mulai, umur awal), sesuaikan standar mingguan bila perlu.
5. **Hak akses**: buat akun petugas, atur izin (mis. matikan *Ubah Tanggal Input*, nyalakan *Karantina*).
6. **Audit**: telusuri siapa mengubah/menghapus apa; **Pulihkan** data terhapus.
7. **Restore Excel**: masukkan data historis dari lembar NF-DAT-002.

### 12.3 Skenario demo untuk calon pembeli (± 10 menit)
| Menit | Peragaan | Titik jual |
|---|---|---|
| 0–1 | Login petugas di HP | Mobile-first, sederhana |
| 1–3 | Input pakan pagi lalu panen telur; beri sengaja hasil timbang kurang | Dashboard merah "Kurang X kg": **deteksi selisih** |
| 3–4 | Input mortalitas, lalu status Sakit → Karantina | Populasi berkurang otomatis, karantina terpisah |
| 4–5 | Dashboard: HD blok vs target umur, fase ayam | Standar mingguan 13–90 minggu |
| 5–6 | Gudang: stok telur terpotong penjualan dari aplikasi kasir | Integrasi |
| 6–7 | Rekap bulan ini + unduh CSV | Laporan siap |
| 7–8 | Login admin: matikan izin "Ubah Tanggal", lihat toggle 64 izin | Kontrol per petugas |
| 8–9 | Hapus satu data → Audit → **Pulihkan** | Jejak dan pemulihan |
| 9–10 | Restore dari Excel | Migrasi data lama |
Siapkan data demo terlebih dahulu dan **jangan** demo di database produksi. Hindari menampilkan: Pengaturan HD/reject (tidak berefek), Hak akses gudang (celah), `/seed-standards`.

---

## 13. Matriks fitur

| Fitur | Ada? | Catatan |
|---|:--:|---|
| Login username/email, remember me | Ya | Tanpa throttle, tanpa lupa password |
| Peran admin/user + 64 izin granular | Ya | Ada celah (3.3) |
| Dashboard harian multi-kartu | Ya | |
| HD harian per blok vs target umur | Ya | Memakai populasi saat ini |
| Standar mingguan 13–90 minggu (dapat diedit) | Ya | Sumber tidak dicantumkan, 4 angka janggal |
| Perbandingan realisasi vs perkiraan (telur, pakan) | Ya | |
| Input telur, pakan, mortalitas, bobot, obat (mobile) | Ya | |
| Karantina (sakit/sembuh) + nomor baterai | Ya | Kematian karantina tidak masuk mortalitas |
| Gudang telur/pakan/obat/karantina | Ya | |
| Stok live tanpa input stok manual untuk konsumsi | Ya | |
| Katalog 26 obat + stok | Ya | Tidak bisa ditambah dari UI; stok awal fiktif |
| Integrasi penjualan (nochifram) | Ya | Wajib nochifram |
| Rekap (preset periode, M1–M4, per blok, grafik) | Ya | Ada nilai cadangan palsu |
| Ekspor CSV 6 tab | Ya | CSV, bukan .xlsx |
| Cetak/PDF laporan | **Tidak** | |
| Audit log otomatis + pulihkan data | Ya | Hash password ikut tercatat |
| Restore Excel NF-DAT-002 | Sebagian | Terikat Kloter 1 / blok A–F, afkir tidak diimpor |
| Edit/hapus data historis | Sebagian | Menimpa timbang; populasi tidak disesuaikan |
| Master kloter/blok/info farm | Ya | Kode blok tidak tersimpan dari form |
| Pengaturan sistem | Sebagian | Sebagian besar tanpa efek |
| GPS / foto / peta | **Tidak** | Bagian 8 |
| Notifikasi WhatsApp/email | **Tidak** | |
| PWA / offline | **Tidak** | Bergantung CDN |
| API untuk aplikasi mobile | **Tidak** | Sanctum terpasang, tanpa route API |
| Multi-farm / multi-tenant | **Tidak** | |
| Backup DB dari aplikasi | **Tidak** | Hanya audit dan restore Excel |
| Grafik tren produksi dan prediksi | Sebagian | Tren ada di Rekap dan Gudang, tanpa prediksi |
| Multi-bahasa | **Tidak** | Hanya Indonesia |
| Tes otomatis | Sebagian | 28 tes, berjalan di DB asli |
| Dokumentasi pengguna | **Tidak** | README bawaan Laravel |

---

## 14. Penilaian kelayakan jual

Skala 1–5 (5 = siap jual). Dua baris baru (akurasi data) dan perubahan skor dijelaskan di catatan.

| Aspek | Nilai | Catatan |
|---|:--:|---|
| Kelengkapan fitur | **5** | Input, gudang, rekap, standar, audit, restore, izin: jarang lengkap di aplikasi peternakan lokal |
| Kedalaman domain | **5** | HD, standar per umur, konversi peti/kg, karantina, bobot sampel dan keseragaman, katalog obat dengan jadwal vaksin |
| Hak akses dan audit | **4** | 64 izin dan audit-restore kuat (turun dari 5: fail-open, 7 izin tak berfungsi, hash sandi di audit) |
| Akurasi dan integritas data | **2** | Empat definisi "peti", populasi bisa melenceng, edit menimpa timbang, nilai cadangan palsu |
| Keamanan | **2** | Dua route publik berbahaya, fail-open, tanpa throttle, `APP_DEBUG=true`, hash sandi di audit |
| Kualitas kode | 3 | Fungsional, tapi controller sampai 2.019 baris, logika dobel, DDL saat runtime |
| Pengujian | **2** | 28 tes bermakna, tetapi berjalan di DB asli dan tidak menyentuh rekap/restore/gudang (turun dari 3) |
| Kemandirian | **2** | Tidak bisa dipasang sendiri; tanpa seeder admin |
| Siap produksi | 2 | Debug aktif, CDN runtime, log 2,5 MB, tanpa panduan |
| Dokumentasi | 1 | README bawaan |
| **Rata-rata** | **±2,8** | Turun dari 3,1 karena temuan baru, bukan karena fitur berkurang |

### Harga dan posisi
- **Apa adanya**: Rp 25–60 juta sekali putus (kisaran lama dipertahankan). Batas atas hanya pantas bila pembeli diberi demo dan syarat bahwa celah di bagian 15 ditutup.
- **Setelah perbaikan bagian 18**: Rp 60–100 juta, ditambah paket pemasangan dan pelatihan.
- **Langganan** (jika dijadikan multi-farm): Rp 300–750 ribu per bulan per farm. Syarat: multi-tenant, notifikasi, dan dukungan.
- **Dalam bundel 4 aplikasi**: aplikasi inti bundel; nilainya naik karena terhubung penjualan, keuangan, dan situs.
- Pembeda utama: **pengendalian internal** (adu timbang vs butir, izin per petugas, audit-restore), bukan sekadar pencatatan.

---

## 15. Temuan masalah dan risiko

Berurut dari yang paling penting. **[BARU]** = baru di versi ini. **[KOREKSI]** = memperbaiki dokumen sebelumnya. Semua berdasar baca kode; `[verifikasi]` sebelum diperbaiki.

### Kritis dan tinggi
1. **Dua route publik tanpa login** — [routes/web.php](../routes/web.php): `/clear-cache` (menjalankan `view:clear`, `cache:clear`, `config:clear`) dan `/seed-standards`. **[KOREKSI]** Seeder yang dipanggil `/seed-standards` memanggil `WeeklyStandard::query()->delete()` lalu mengisi ulang: **siapa pun di internet bisa menghapus semua penyesuaian standar** (bukan sekadar "berpotensi"). Hapus atau lindungi (admin / perintah artisan).
2. **Tes berjalan di database asli** **[BARU]** — [phpunit.xml](../phpunit.xml) menonaktifkan sqlite, tidak ada `RefreshDatabase`; `.env` menunjuk `nochifram`. `CrudAuditTest` memakai `User::where('role','admin')->first()` asli, membuat flock/blok/pengguna uji bila tidak ada, dan menghapus data. Log memuat `testing.ERROR` terhadap `audit_logs` yang sama. **Jangan jalankan `php artisan test` di DB berisi data.**
3. **Tidak bisa dipasang di DB kosong** — kolom `users` (`username, role, is_active, permissions, phone, avatar`) dan tabel `settings` tidak dibuat di sini; **tidak ada seeder admin** **[BARU: seeder admin]**.
4. **Sistem izin fail-open dan sebagian tidak berfungsi** **[BARU]** — bagian 3.3: `feature_warehouse_manage` tak terdaftar sehingga semua pengguna Gudang bisa ubah/hapus mutasi; 7 izin tak dipakai; aksi tulis Master hanya dijaga `menu_master`.
5. **Pengecekan admin di Rekap dinonaktifkan** **[KOREKSI]** — `requireAdmin()` kosong. Dokumen lama menyebut pengecekan ada.
6. **Akun dan password lemah** — password pengguna baru min 4, ganti password min 6; tanpa throttle login; tanpa akun awal aman.
7. **`APP_DEBUG=true`, `APP_NAME=Laravel`**, log `debug`.
8. **Kata sandi (hash) tersimpan di audit log** **[BARU]** — 10.4.
9. **Nilai cadangan palsu tampil sebagai data** **[BARU]** — Rekap: reject 1,2%, mortalitas 2,09%/0,50%, bobot 1,60 kg, fase "Grower" untuk M1; Input: populasi kloter 2.249/1.768; Dashboard: bobot 1,620 kg. Tampil walau data kosong, bisa dikira nyata oleh investor.
10. **Populasi melenceng** **[BARU]** — 7.9: ubah/hapus mortalitas tak menyesuaikan, pulihkan menggandakan pengurangan, "mati/sakit" lebih besar dari populasi diabaikan diam-diam, `flocks.current_population` basi.
11. **Stok awal obat fiktif otomatis** **[BARU]** — 5.5.
12. **Konstanta peti tidak konsisten** **[BARU]** — 7.16.

### Sedang
13. **DDL saat runtime dan skema tak lengkap** — `AuditLog`, `Quarantine::ensureTableExists`, `WeightSample::ensureColumnsExist` membuat tabel/kolom di tengah permintaan; kolom `weight_samples.sample_index` **tidak ada di migration mana pun**. Dua migration `audit_logs` saling tumpang tindih. Rapikan jadi migration murni.
14. **Pengaturan yang tidak berefek** **[BARU]** — 5.4 (hd_warning, hd_minimum, reject_maximum, empat `standard_*`; `hd_target` global diabaikan).
15. **Edit rekap menimpa hasil timbang** **[BARU]** — 7.15.
16. **Kematian di karantina tidak masuk statistik mortalitas** **[BARU]** — 7.10.
17. **Restore Excel**: default timpa; `cleanupDuplicateFlocksAndCoops` menghapus data tanpa konfirmasi dan terikat "Kloter 1/Blok A–C"; baris tak dikenal dilewati diam-diam; ekstensi hanya dicek dari nama; `.txt` diterima; afkir tak diimpor; berat/peti diestimasi.
18. **Tanpa transaksi database** pada input multi-tabel (mortalitas + karantina + populasi, gudang). Hanya restore Excel yang memakai transaksi.
19. **Perhitungan di memori (`get()` lalu filter di PHP)** di Gudang dan Rekap: melambat dengan data bertahun-tahun; HD/HDP memakai populasi saat ini.
20. **Dependensi CDN tanpa versi terkunci** (`lucide@latest`, `chart.js` tanpa versi) dan **Tailwind Play CDN** (tidak untuk produksi): tampilan bisa berubah/rusak sendiri dan mati tanpa internet **[BARU]**.
21. **Pembagian pakan Layer vs Grower dari kata kunci nama** dan stok obat dari parsing teks dosis: rapuh **[BARU]**.
22. **Standar produksi: 4 angka janggal (minggu 43, 52, 89, 90) dan sumber tak dicantumkan** **[BARU]** — 5.3.
23. **Tanggal input bebas** (lampau/masa depan) untuk petugas dengan izin default `input_filter_tanggal` **[BARU]**.
24. **Hard-coded**: blok A–F, "6 blok", Kloter 1/2, isi tray 30, jenis pakan Layer/Grower/Starter, daftar obat. Farm lain butuh pengaturan.

### Rendah
25. Controller 2.019 baris sulit dirawat; logika dobel antara Dashboard, Rekap, Gudang.
26. README bawaan Laravel; tanpa panduan.
27. `storage/logs/laravel.log` ±2,5 MB di folder proyek (jangan ikut terjual); `.phpunit.result.cache` ikut.
28. Dua `ExampleTest` bawaan tidak bermakna.
29. Branding "NOCHI FARM", "Nochi Farm" tertanam (seeder, nama email otomatis `@nochifarm.com`, link demo di komentar).
30. Seeder demo tertinggal tanggal 2025 dan bergantung user `petugas` yang tidak ada.
31. Kode blok tidak tersimpan dari form tambah/ubah blok (field `code` tidak divalidasi).
32. `phpoffice/phpspreadsheet` dipasang dengan versi `*` (tidak terkunci).

---

## 16. Yang bisa dibanggakan dan batasan yang harus diakui

### Bisa dibanggakan saat menjual (benar dan terverifikasi dari kode)
- **Adu timbang vs butir**: dashboard membandingkan hasil timbang dengan estimasi dari jumlah butir × berat telur standar umur, merah/hijau per blok.
- **Standar mingguan 13–90 minggu** (HD, berat telur, pakan, bobot min/target/maks) yang bisa diedit, dengan fase dan pesan kondisi otomatis, umur ayam dihitung otomatis dari tanggal masuk.
- **Hak akses sampai level kartu dan tombol** (64 izin) dan akun petugas yang bisa dinonaktifkan.
- **Audit otomatis dan pemulihan data terhapus** untuk 13 jenis data.
- **Gudang terhubung**: stok telur dan pakan dikurangi penjualan dari aplikasi kasir; stok pakan otomatis dari input harian.
- **Katalog 26 obat/vaksin** dengan dosis, cara, jadwal, indikasi; stok obat terpotong otomatis saat input.
- **Karantina** dengan nomor baterai dan pemulihan populasi.
- **Restore data lama dari Excel** (migrasi dari pencatatan manual).
- **Input mobile cepat** dengan penjagaan input ganda untuk telur dan pakan.
- **28 tes** untuk login, izin, CRUD dan audit, konversi 10 kg = 1 peti.

### Batasan yang harus diakui ke calon pembeli (jangan disembunyikan)
- Tidak ada GPS, foto bukti, peta, notifikasi, PWA/offline, cetak/PDF, API mobile, multi-farm.
- Butuh aplikasi nochifram (tabel `users`, `settings`, penjualan) di DB yang sama; belum bisa dipasang sendiri.
- Tampilan bergantung internet (CDN); di kandang tanpa sinyal akan rusak.
- Sebagian pengaturan belum berfungsi; sumber standar produksi belum dicantumkan.
- Definisi "peti" masih perlu diseragamkan; populasi perlu diperiksa berkala sampai perbaikan selesai.
- Belum ada dokumentasi pengguna; pelatihan dibutuhkan.

---

## 17. Instalasi, deploy, dan pengujian

### 17.1 Kebutuhan server
| Komponen | Minimum |
|---|---|
| PHP | ≥ 8.1 (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `zip`, `gd` untuk PhpSpreadsheet) |
| Database | MySQL/MariaDB (ada query khusus MySQL) |
| Web server | Apache/Nginx, document root `public/` |
| Composer | 2.x (`composer.lock` ada) |
| Node | **Tidak diperlukan** untuk produksi (Tailwind dari CDN); `package.json` dan `vite.config.js` tersedia tetapi tidak dipakai tampilan |
| Internet | Diperlukan di browser pengguna untuk CDN |

### 17.2 Urutan pasang (setelah perbaikan 18)
1. `composer install --no-dev --optimize-autoloader`
2. Salin `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `DB_*` (DB **yang sama** dengan nochifram atau DB baru setelah migration `users`/`settings` ditambahkan), `SESSION_DRIVER`, `LOG_LEVEL=warning`; `php artisan key:generate`.
3. **Jalankan migration aplikasi pemilik tabel bersama lebih dulu** (nochifram: `users`, `settings`, `sales`, `sale_items`, `trips`, `audit_logs`), lalu migration aplikasi ini (flocks, coops, produksi, standar, izin, karantina, audit).
4. `php artisan db:seed` (pengaturan + standar). **Buat admin** (belum ada seeder).
5. Masuk sebagai admin → Master: Info Farm, Kloter dan Blok, Hak akses → buat akun petugas.
6. `php artisan config:cache`, `route:cache`, `view:cache` (setelah route publik dihapus).
7. Jangan menjalankan seeder demo di produksi (`FarmSeeder`, `RekapSeeder`, `WarehouseSeeder`).

Catatan: `.env` saat ini memakai `DB_DATABASE=nochifram`, `CACHE_DRIVER=file`, `SESSION_DRIVER=file`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=smtp` (belum dikonfigurasi; tanpa lupa password).

### 17.3 Pengujian — isi dan cakupan (dibaca, tidak dijalankan)
| Berkas | Jumlah | Mencakup |
|---|---:|---|
| [AuthAndPermissionTest](../tests/Feature/AuthAndPermissionTest.php) | 10 | Halaman login, login sukses/gagal, user nonaktif ditolak, admin akses penuh, user diblok saat izin dimatikan, toggle izin via AJAX, logout, toggle massal per kategori, penyaringan kartu dashboard/gudang menurut izin |
| [CrudAuditTest](../tests/Feature/CrudAuditTest.php) | 10 | Semua GET route terbuka; CRUD telur, pakan, mortalitas, bobot, obat, stok gudang, kloter dan blok; update standar dan pengaturan; aturan **10 kg = 1 peti** dan tampilan perbandingan |
| [MedicineCatalogServiceTest](../tests/Unit/MedicineCatalogServiceTest.php) | 8 | 8 kategori, 26 produk, atribut, pencocokan nama/ID, ringkasan, parsing dosis, kunci kategori |
| ExampleTest (Feature + Unit) | 2 | Bawaan Laravel, tidak bermakna |
| **Total** | **30 (28 bermakna)** | |

**Tidak teruji:** rekap dan ekspor CSV, restore Excel, audit-restore, populasi pasca-edit, gudang karantina, HD dan rumus perbandingan, dashboard multi-hari, unduh template.

**Mengapa tidak dijalankan:** tes memakai database `.env` (DB asli `nochifram`) tanpa isolasi (lihat 15 poin 2); menjalankannya di mesin berisi data dapat menambah/menghapus data. Untuk menjalankannya aman: buat DB khusus tes, set `DB_DATABASE` di `.env.testing`/`phpunit.xml`, atau pakai sqlite memori + `RefreshDatabase` (butuh seeding minimal karena tes bergantung data yang sudah ada).

---

## 18. Checklist sebelum dijual

### Wajib (keamanan dan kebenaran data)
- [ ] Hapus atau amankan `/clear-cache` dan `/seed-standards` (admin only / perintah artisan)
- [ ] Jadikan `canAccess` **fail-closed** (kunci tak dikenal = tolak); daftarkan `feature_warehouse_manage` atau ganti dengan `warehouse_btn_manage`
- [ ] Pasang izin nyata untuk 5 kunci `feature_master_*` dan `feature_rekap_view`, atau hapus toggle yang tak berefek
- [ ] Satu middleware `admin` untuk aksi pengguna, izin, restore Excel, dan hapus master
- [ ] Isolasi tes (DB terpisah, `RefreshDatabase`), dan jalankan seluruh tes
- [ ] Tambah migration `users` (kolom lengkap) dan `settings`; buat seeder/perintah admin awal
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_NAME`; bersihkan `laravel.log`
- [ ] Throttle login, perketat password (≥ 8), tambah lupa password bila perlu
- [ ] Jangan simpan hash sandi di audit (kecualikan atribut sensitif)
- [ ] **Seragamkan definisi peti** (satu konstanta/fungsi) dan perbaiki semua `/25`, `×25`, `×250`, `/30`, `×16`
- [ ] Sesuaikan populasi saat ubah/hapus/pulihkan mortalitas dan saat karantina mati; hitung ulang `flocks.current_population`
- [ ] Edit rekap telur tidak boleh menimpa hasil timbang asli
- [ ] Hilangkan nilai cadangan palsu (1,2%, 2,09%, 0,50%, 1,60 kg, 2.249/1.768): tampilkan "belum ada data"
- [ ] Hapus pembuatan stok awal obat fiktif otomatis
- [ ] Bungkus operasi multi-tabel dengan `DB::transaction`
- [ ] Konfirmasi, pratinjau, dan cadangan sebelum restore Excel yang menimpa; ganti pembersihan duplikat yang menghapus data
- [ ] Periksa dan perbaiki angka standar minggu 43, 52, 89, 90, dan cantumkan sumber standar

### Sebaiknya (nilai jual)
- [ ] Ganti CDN dengan aset lokal (build Vite/Tailwind, chart.js dan lucide terkunci versi); tambah PWA agar bisa dipakai saat sinyal lemah
- [ ] Hubungkan atau hapus pengaturan tanpa efek (`hd_warning`, `hd_minimum`, `reject_maximum`, `standard_*`)
- [ ] Foto bukti timbang dan lokasi pada input telur/pakan (peluang pembeda)
- [ ] Notifikasi WhatsApp/email: HD turun, kematian tinggi, stok pakan/obat menipis
- [ ] Cetak/PDF laporan harian dan mingguan; ekspor `.xlsx` sungguhan
- [ ] Katalog obat di DB dengan antarmuka tambah/ubah, dan stok obat tidak dari teks bebas
- [ ] Pengaturan farm tidak hardcode (jumlah blok, jenis pakan, isi peti)
- [ ] Mode "tanpa modul penjualan" dan pasang mandiri
- [ ] Rekap: hitung agregat di SQL, populasi historis per tanggal
- [ ] Pecah controller; pindahkan rumus ke service dan tes
- [ ] Batasi tanggal input (mis. maksimal H−3) untuk petugas

### Dokumen serah terima
- [ ] README instalasi dan urutan migration lintas aplikasi
- [ ] Manual petugas (alur harian 12.1) dan manual admin (12.2) bergambar
- [ ] Daftar 64 izin dan artinya (bagian 9, dari registri)
- [ ] Diagram tabel dan hubungan antar aplikasi (6.3, 11)
- [ ] Penjelasan rumus (bagian 7) untuk audit pembeli
- [ ] Template NF-DAT-002 dan contoh data
- [ ] Data demo yang aman dan skrip demo (12.3)
- [ ] Lisensi dan ketentuan penggunaan, syarat dukungan

---

## 19. Rencana perbaikan

| Fase | Pekerjaan | Perkiraan |
|---|---|---|
| 1 — Tutup celah | Route publik, fail-closed izin, middleware admin, debug off, throttle, hash sandi di audit, isolasi tes | 1–2 hari |
| 2 — Kemandirian | Migration `users` dan `settings`, seeder/perintah admin, uji pasang di DB kosong | 1 hari |
| 3 — Kebenaran data | Satu definisi peti, populasi konsisten (mortalitas, restore, karantina), hapus nilai palsu, stok awal fiktif, transaksi, edit rekap tidak menimpa timbang | 3–4 hari |
| 4 — Tes | Tes untuk rekap, rumus perbandingan, populasi, restore Excel, gudang; jalankan semua di DB terpisah | 2–3 hari |
| 5 — Nilai jual | Aset lokal + PWA, foto/GPS bukti, notifikasi, cetak/PDF, katalog obat di DB | 5–8 hari |
| 6 — Dokumentasi | README, manual per peran, daftar izin, demo data, skrip demo | 2 hari |

Perkiraan hanya urutan besaran untuk satu developer yang mengenal kode; belum diuji.

---

## 20. Untuk analisis lanjutan dan keterbatasan dokumen

**Bahan turunan:** proposal penjualan (ringkasan bagian 1, kekuatan bagian 16, harga bagian 14), manual pengguna (bagian 12), spesifikasi teknis (bagian 2, 6, 7, 11), daftar izin (bagian 9), checklist serah terima (bagian 18), perbandingan dengan 3 aplikasi lain dalam paket bundel.

**Keterbatasan dokumen ini (jujur):**
- Berdasarkan baca kode statis; **aplikasi tidak dijalankan**, tampilan tidak dilihat di browser, tangkapan layar tidak ada.
- Tes tidak dijalankan (risiko data, bagian 17.3); status lulus/gagal tidak diketahui.
- Isi tabel `settings` dan `users` di database produksi tidak diperiksa; nilai awal di bagian 5 berasal dari seeder.
- Tampilan sebagian view (modal rinci, teks tombol) dibaca sebagian; daftar komponen berdasarkan ID, label, dan izin yang terlihat.
- Standar produksi (5.3) dicatat apa adanya dari seeder; kebenaran angka terhadap standar breeder tidak diverifikasi.
- Estimasi harga dan waktu perbaikan adalah perkiraan kasar pasar, bukan penilaian resmi.

*Konfirmasi setiap temuan dengan uji jalan di lingkungan staging (dengan DB salinan) sebelum menyatakannya pasti kepada pembeli.*
