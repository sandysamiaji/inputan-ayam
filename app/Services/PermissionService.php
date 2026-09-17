<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPermission;

class PermissionService
{
    /**
     * Master daftar menu dan seluruh fitur yang dapat diatur hak aksesnya (Toggle Switch)
     * Sangat detail mencakup setiap kartu, section, dan widget di Dashboard, Gudang, Input, Rekap, dan Master.
     */
    public static function getAllPermissions(): array
    {
        return [
            // 1. NAVIGATOR MENU UTAMA
            'menu' => [
                'label' => '1. Navigator Menu Utama (Navbar & Bottom Bar)',
                'icon' => 'layout-grid',
                'items' => [
                    'menu_dashboard' => [
                        'label' => 'Menu Dashboard',
                        'desc' => 'Menampilkan link navigasi dan halaman Dashboard ringkasan',
                        'default' => true,
                    ],
                    'menu_warehouse' => [
                        'label' => 'Menu Gudang',
                        'desc' => 'Menampilkan link navigasi dan halaman Gudang',
                        'default' => true,
                    ],
                    'menu_input' => [
                        'label' => 'Menu Input Cepat Mobile',
                        'desc' => 'Menampilkan link navigasi tombol (+) dan halaman Input Mobile',
                        'default' => true,
                    ],
                    'menu_rekap' => [
                        'label' => 'Menu Rekap & Laporan',
                        'desc' => 'Menampilkan link navigasi dan halaman Rekapitulasi Data',
                        'default' => true,
                    ],
                    'menu_master' => [
                        'label' => 'Menu Master Data',
                        'desc' => 'Menampilkan link navigasi dan halaman Master Konfigurasi Farm',
                        'default' => false,
                    ],
                ],
            ],

            // 2. DASHBOARD: KARTU RINGKASAN HARI INI
            'dashboard_cards' => [
                'label' => '2. Dashboard: Filter & Kartu Ringkasan Hari Ini',
                'icon' => 'activity',
                'items' => [
                    'dash_filter' => [
                        'label' => 'Filter Klotter & Pemilih Tanggal',
                        'desc' => 'Widget pemilih klotter aktif di dashboard',
                        'default' => true,
                    ],
                    'dash_filter_tanggal' => [
                        'label' => 'Pemilih Tanggal Dashboard',
                        'desc' => 'Akses untuk mengubah tanggal (Pilih Tanggal) di dashboard',
                        'default' => true,
                    ],
                    'dash_card_egg' => [
                        'label' => 'Kartu Produksi Telur Hari Ini',
                        'desc' => 'Menampilkan metrik panen telur (Peti & Butir) hari ini',
                        'default' => true,
                    ],
                    'dash_card_feed' => [
                        'label' => 'Kartu Pemakaian Pakan Hari Ini',
                        'desc' => 'Menampilkan metrik pemakaian pakan (Kg & Jenis Pakan) hari ini',
                        'default' => true,
                    ],
                    'dash_card_mortality' => [
                        'label' => 'Kartu Mortalitas Ayam Hari Ini',
                        'desc' => 'Menampilkan metrik jumlah ayam mati/afkir harian',
                        'default' => true,
                    ],
                    'dash_card_weight' => [
                        'label' => 'Kartu Berat Badan Ayam',
                        'desc' => 'Menampilkan rata-rata sampel bobot badan (Kg) terkini',
                        'default' => true,
                    ],
                    'dash_card_health' => [
                        'label' => 'Kartu Vaksin & Obat Hari Ini',
                        'desc' => 'Menampilkan ringkasan kegiatan perlakuan medis hari ini',
                        'default' => true,
                    ],
                ],
            ],

            // 3. DASHBOARD: STATUS BLOK KANDANG & ANALISA
            'dashboard_coops' => [
                'label' => '3. Dashboard: Status Blok Kandang & Analisa HD',
                'icon' => 'home',
                'items' => [
                    'dash_section_coops' => [
                        'label' => 'Section Status Blok Kandang Aktif',
                        'desc' => 'Menampilkan kontainer daftar kartu seluruh blok kandang (Blok A–F)',
                        'default' => true,
                    ],
                    'dash_coop_hd' => [
                        'label' => 'Badge & Angka Hen-Day (HD) Blok',
                        'desc' => 'Menampilkan persentase performa HD dan status fase umur per blok',
                        'default' => true,
                    ],
                    'dash_coop_standards' => [
                        'label' => 'Acuan Telur & Standar Pakan Blok',
                        'desc' => 'Menampilkan target berat telur dan standar gram pakan per ekor',
                        'default' => true,
                    ],
                    'dash_coop_egg_comparison' => [
                        'label' => 'Realisasi Panen Telur vs Perkiraan',
                        'desc' => 'Kotak perbandingan hitungan aplikasi vs input karyawan (Merah/Hijau)',
                        'default' => true,
                    ],
                    'dash_coop_feed_comparison' => [
                        'label' => 'Standar Pakan vs Realisasi Input Pakan',
                        'desc' => 'Kotak hitungan kebutuhan pakan harian blok vs realisasi pakan yang diinput',
                        'default' => true,
                    ],
                    'dash_coop_fase_info' => [
                        'label' => 'Penjelasan Alasan Fase Umur Ayam',
                        'desc' => 'Teks keterangan fase (Puncak Produksi, Grower, dll) dengan tombol detail',
                        'default' => true,
                    ],
                ],
            ],

            // 4. DASHBOARD: GUDANG & INTEGRASI PENJUALAN
            'dashboard_sidebar' => [
                'label' => '4. Dashboard: Kolom Gudang & Timeline Aktivitas',
                'icon' => 'database',
                'items' => [
                    'dash_section_warehouse_summary' => [
                        'label' => 'Section Gudang & Integrasi Penjualan',
                        'desc' => 'Kontainer ringkasan stok telur & pakan di kolom kanan desktop',
                        'default' => true,
                    ],
                    'dash_widget_egg_stock' => [
                        'label' => 'Widget Stok Telur Saat Ini',
                        'desc' => 'Menampilkan angka stok telur terkini, telur masuk, dan telur keluar/terjual',
                        'default' => true,
                    ],
                    'dash_widget_feed_stock' => [
                        'label' => 'Widget Stok Pakan Saat Ini',
                        'desc' => 'Menampilkan angka stok pakan terkini, konsumsi kandang, dan pakan terjual',
                        'default' => true,
                    ],
                    'dash_section_recent_activity' => [
                        'label' => 'Timeline Aktivitas Terakhir',
                        'desc' => 'Menampilkan log pergerakan real-time: produksi, pakan, obat, mutasi & penjualan kasir',
                        'default' => true,
                    ],
                ],
            ],

            // 6. GUDANG: RINGKASAN STOK & TAB KATEGORI
            'warehouse_summary' => [
                'label' => '6. Gudang: Ringkasan Stok & Tab Kategori',
                'icon' => 'warehouse',
                'items' => [
                    'warehouse_card_summary' => [
                        'label' => 'Kartu Ringkasan Stok Barang',
                        'desc' => 'Tiga kartu ringkasan stok telur, pakan, dan obat di bagian atas halaman gudang',
                        'default' => true,
                    ],
                    'warehouse_filter_tanggal' => [
                        'label' => 'Pemilih Tanggal Gudang',
                        'desc' => 'Akses untuk memilih rentang tanggal di halaman Gudang',
                        'default' => true,
                    ],
                    'warehouse_click_telur' => [
                        'label' => 'Klik Kartu Gudang Telur',
                        'desc' => 'Akses untuk mengklik kartu Gudang Telur menuju detail',
                        'default' => true,
                    ],
                    'warehouse_click_pakan' => [
                        'label' => 'Klik Kartu Gudang Pakan',
                        'desc' => 'Akses untuk mengklik kartu Gudang Pakan menuju detail',
                        'default' => true,
                    ],
                    'warehouse_click_obat' => [
                        'label' => 'Klik Kartu Gudang Obat',
                        'desc' => 'Akses untuk mengklik kartu Gudang Obat menuju detail',
                        'default' => true,
                    ],
                    'feature_warehouse_telur' => [
                        'label' => 'Lihat Gudang Telur',
                        'desc' => 'Membuka tab dan detail data stok serta mutasi telur utuh/rusak',
                        'default' => true,
                    ],
                    'feature_warehouse_pakan' => [
                        'label' => 'Lihat Gudang Pakan',
                        'desc' => 'Membuka tab dan detail data stok pakan masuk/keluar',
                        'default' => true,
                    ],
                    'feature_warehouse_obat' => [
                        'label' => 'Lihat Gudang Obat & Vaksin',
                        'desc' => 'Membuka tab dan detail data stok obat dan suplemen',
                        'default' => true,
                    ],
                ],
            ],

            // 7. GUDANG: TREN ALIRAN & 8 DATA STREAM
            'warehouse_streams' => [
                'label' => '7. Gudang: Tren Aliran & 8 Data Stream',
                'icon' => 'trending-up',
                'items' => [
                    'warehouse_chart_trends' => [
                        'label' => 'Tren Semua Aliran Barang (Grafik)',
                        'desc' => 'Grafik visualisasi pergerakan 14 hari (Masuk, Digunakan, Keluar, Terjual)',
                        'default' => true,
                    ],
                    'warehouse_stream_pills' => [
                        'label' => 'Akses Langsung 8 Aliran Data Gudang',
                        'desc' => 'Tombol-tombol pill pintas (Telur Masuk, Rusak, Terjual, Pakan Masuk, dll)',
                        'default' => true,
                    ],
                ],
            ],

            // 8. GUDANG: MUTASI & PENJUALAN KASIR
            'warehouse_mutations' => [
                'label' => '8. Gudang: Mutasi & Penjualan Kasir',
                'icon' => 'shopping-cart',
                'items' => [
                    'warehouse_recent_mutations' => [
                        'label' => 'Tabel Mutasi Terkini Gudang',
                        'desc' => 'Menampilkan riwayat catatan pergerakan barang masuk & keluar farm',
                        'default' => true,
                    ],
                    'warehouse_btn_add' => [
                        'label' => 'Tombol Tambah Mutasi Barang',
                        'desc' => 'Tombol untuk mencatat mutasi stok masuk/keluar baru',
                        'default' => true,
                    ],
                    'warehouse_btn_manage' => [
                        'label' => 'Aksi Edit & Hapus Mutasi Gudang',
                        'desc' => 'Tombol Edit dan Hapus pada setiap baris data mutasi gudang',
                        'default' => true,
                    ],
                    'warehouse_sales_stream' => [
                        'label' => 'Barang Keluar: Penjualan Real-Time Kasir',
                        'desc' => 'Menampilkan daftar penjualan yang otomatis terhubung ke sistem kasir nochifram',
                        'default' => true,
                    ],
                ],
            ],

            // 9. MODUL INPUT MOBILE
            'input_forms' => [
                'label' => '9. Modul Input Transaksi (Mobile)',
                'icon' => 'edit-3',
                'items' => [
                    'input_filter_tanggal' => [
                        'label' => 'Ubah Tanggal Input',
                        'desc' => 'Akses untuk mengubah tanggal transaksi pada halaman Input',
                        'default' => true,
                    ],
                    'input_form_egg' => [
                        'label' => 'Pilihan Transaksi: Produksi Telur',
                        'desc' => 'Kartu dan formulir input panen telur di halaman input mobile',
                        'default' => true,
                    ],
                    'input_form_feed' => [
                        'label' => 'Pilihan Transaksi: Pemakaian Pakan',
                        'desc' => 'Kartu dan formulir input konsumsi pakan di halaman input mobile',
                        'default' => true,
                    ],
                    'input_form_mortality' => [
                        'label' => 'Pilihan Transaksi: Mortalitas Ayam',
                        'desc' => 'Kartu dan formulir input kematian/afkir di halaman input mobile',
                        'default' => true,
                    ],
                    'input_form_weight' => [
                        'label' => 'Pilihan Transaksi: Bobot Ayam',
                        'desc' => 'Kartu dan formulir input bobot timbangan di halaman input mobile',
                        'default' => true,
                    ],
                    'input_form_health' => [
                        'label' => 'Pilihan Transaksi: Vaksin & Obat',
                        'desc' => 'Kartu dan formulir input perlakuan obat di halaman input mobile',
                        'default' => true,
                    ],
                ],
            ],

            // 10. MODUL REKAP & LAPORAN
            'rekap' => [
                'label' => '10. Modul Rekap Data & Laporan',
                'icon' => 'clipboard-list',
                'items' => [
                    'rekap_filter' => [
                        'label' => 'Filter Blok Rekap',
                        'desc' => 'Widget filter kandang di modul rekap',
                        'default' => true,
                    ],
                    'rekap_filter_tanggal' => [
                        'label' => 'Filter Tanggal Rekap',
                        'desc' => 'Akses untuk mengubah rentang tanggal di halaman Rekap',
                        'default' => true,
                    ],
                    'feature_rekap_view' => [
                        'label' => 'Tabel Rekapitulasi Data Harian',
                        'desc' => 'Menampilkan tabel rekap produksi, pakan, dan mortalitas harian & bulanan',
                        'default' => true,
                    ],
                    'feature_rekap_detail' => [
                        'label' => 'Halaman Detail Rekap Kandang',
                        'desc' => 'Membuka halaman analisa detail dan grafik performa per blok',
                        'default' => true,
                    ],
                    'feature_rekap_export' => [
                        'label' => 'Tombol Export Laporan Excel',
                        'desc' => 'Tombol untuk mengunduh laporan rekapitulasi format .xlsx',
                        'default' => true,
                    ],
                    'feature_rekap_manage' => [
                        'label' => 'Edit & Hapus Data Historis Rekap',
                        'desc' => 'Tombol koreksi dan penghapusan data produksi/pakan lama pada tabel rekap',
                        'default' => false,
                    ],
                ],
            ],

            // 11. MODUL MASTER DATA
            'master' => [
                'label' => '11. Modul Master Data & Konfigurasi',
                'icon' => 'settings',
                'items' => [
                    'feature_master_info_farm' => [
                        'label' => 'Informasi Profil Peternakan',
                        'desc' => 'Melihat dan mengubah profil informasi peternakan',
                        'default' => false,
                    ],
                    'feature_master_coops' => [
                        'label' => 'Master Flock & Blok Kandang',
                        'desc' => 'Tambah, edit, dan hapus data klotter, blok, kapasitas, dan populasi ayam',
                        'default' => false,
                    ],
                    'feature_master_standards' => [
                        'label' => 'Standar Produksi, Pakan & Bobot',
                        'desc' => 'Konfigurasi acuan standar mingguan umur 13–90 minggu',
                        'default' => false,
                    ],
                    'feature_master_settings' => [
                        'label' => 'Pengaturan Sistem & Obat',
                        'desc' => 'Pengaturan parameter sistem farm, bobot karung, dan daftar obat',
                        'default' => false,
                    ],
                    'feature_master_permissions' => [
                        'label' => 'Manajemen Hak Akses & Pengguna',
                        'desc' => 'Pengaturan toggle izin seluruh pengguna (Khusus Super Admin)',
                        'default' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Periksa apakah pengguna memiliki hak akses ke fitur/menu tertentu
     */
    public static function canAccess(?User $user, string $permissionKey): bool
    {
        if (!$user) {
            return false;
        }

        // Pengguna non-aktif tidak memiliki akses sama sekali
        if (!$user->is_active) {
            return false;
        }

        // Role 'admin' selalu memiliki akses penuh tanpa batas (Full Access)
        if ($user->role === 'admin') {
            return true;
        }

        // Cari pengaturan spesifik user di tabel user_permissions
        $record = UserPermission::where('user_id', $user->id)
            ->where('permission_key', $permissionKey)
            ->first();

        if ($record !== null) {
            return (bool) $record->is_enabled;
        }

        // Jika belum diset di tabel, gunakan default dari master registry
        foreach (self::getAllPermissions() as $category) {
            if (isset($category['items'][$permissionKey])) {
                return (bool) $category['items'][$permissionKey]['default'];
            }
        }

        return true;
    }
}
