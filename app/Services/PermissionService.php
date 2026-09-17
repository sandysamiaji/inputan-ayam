<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPermission;

class PermissionService
{
    /**
     * Master daftar menu dan fitur yang dapat diatur hak aksesnya (Toggle Switch)
     */
    public static function getAllPermissions(): array
    {
        return [
            'menu' => [
                'label' => 'Menu Utama (Navigasi Atas & Bawah)',
                'icon' => 'layout-grid',
                'items' => [
                    'menu_dashboard' => [
                        'label' => 'Menu Dashboard',
                        'desc' => 'Menampilkan menu dan halaman Dashboard ringkasan',
                        'default' => true,
                    ],
                    'menu_warehouse' => [
                        'label' => 'Menu Gudang',
                        'desc' => 'Menampilkan menu dan halaman Gudang (Telur, Pakan, Obat)',
                        'default' => true,
                    ],
                    'menu_input' => [
                        'label' => 'Menu Input Cepat Mobile',
                        'desc' => 'Menampilkan menu dan halaman Form Input Cepat Kandang',
                        'default' => true,
                    ],
                    'menu_rekap' => [
                        'label' => 'Menu Rekap & Laporan',
                        'desc' => 'Menampilkan menu dan halaman Rekapitulasi Data & Laporan',
                        'default' => true,
                    ],
                    'menu_master' => [
                        'label' => 'Menu Master Data',
                        'desc' => 'Menampilkan menu dan halaman Konfigurasi Master Data',
                        'default' => false,
                    ],
                ],
            ],
            'quick_actions' => [
                'label' => 'Aksi Cepat Input Kandang (Dashboard & Input)',
                'icon' => 'zap',
                'items' => [
                    'feature_quick_egg' => [
                        'label' => 'Input Produksi Telur',
                        'desc' => 'Akses modal & form pencatatan telur (Peti & Kg)',
                        'default' => true,
                    ],
                    'feature_quick_feed' => [
                        'label' => 'Input Pemakaian Pakan',
                        'desc' => 'Akses modal & form pencatatan pakan harian',
                        'default' => true,
                    ],
                    'feature_quick_mortality' => [
                        'label' => 'Input Kematian / Afkir',
                        'desc' => 'Akses modal & form pencatatan ayam mati atau afkir',
                        'default' => true,
                    ],
                    'feature_quick_weight' => [
                        'label' => 'Input Timbang Bobot',
                        'desc' => 'Akses modal & form sampel bobot badan mingguan',
                        'default' => true,
                    ],
                    'feature_quick_health' => [
                        'label' => 'Input Vaksin & Obat',
                        'desc' => 'Akses modal & form perlakuan obat/vaksin',
                        'default' => true,
                    ],
                ],
            ],
            'warehouse' => [
                'label' => 'Fitur Modul Gudang',
                'icon' => 'warehouse',
                'items' => [
                    'feature_warehouse_telur' => [
                        'label' => 'Lihat Gudang Telur',
                        'desc' => 'Melihat daftar stok dan mutasi telur',
                        'default' => true,
                    ],
                    'feature_warehouse_pakan' => [
                        'label' => 'Lihat Gudang Pakan',
                        'desc' => 'Melihat daftar stok dan mutasi pakan',
                        'default' => true,
                    ],
                    'feature_warehouse_obat' => [
                        'label' => 'Lihat Gudang Obat/Vaksin',
                        'desc' => 'Melihat daftar stok dan mutasi obat',
                        'default' => true,
                    ],
                    'feature_warehouse_manage' => [
                        'label' => 'Kelola Mutasi Gudang',
                        'desc' => 'Menambah, mengedit, atau menghapus mutasi stok masuk/keluar',
                        'default' => true,
                    ],
                ],
            ],
            'rekap' => [
                'label' => 'Fitur Modul Rekap & Laporan',
                'icon' => 'clipboard-list',
                'items' => [
                    'feature_rekap_view' => [
                        'label' => 'Lihat Rekapitulasi Data',
                        'desc' => 'Melihat tabel rekapitulasi harian & bulanan',
                        'default' => true,
                    ],
                    'feature_rekap_detail' => [
                        'label' => 'Detail Rekap Kandang',
                        'desc' => 'Melihat halaman analisa detail performa per blok',
                        'default' => true,
                    ],
                    'feature_rekap_export' => [
                        'label' => 'Export Laporan Excel',
                        'desc' => 'Mengunduh laporan dalam format Excel (.xlsx)',
                        'default' => true,
                    ],
                    'feature_rekap_manage' => [
                        'label' => 'Edit / Hapus Data Historis',
                        'desc' => 'Koreksi dan penghapusan data produksi/pakan/kematian yang sudah tercatat',
                        'default' => false,
                    ],
                ],
            ],
            'master' => [
                'label' => 'Fitur Master Data & Pengaturan',
                'icon' => 'settings',
                'items' => [
                    'feature_master_info_farm' => [
                        'label' => 'Informasi Farm',
                        'desc' => 'Melihat dan mengubah profil informasi peternakan',
                        'default' => false,
                    ],
                    'feature_master_coops' => [
                        'label' => 'Kelola Flocks & Kandang',
                        'desc' => 'Tambah, edit, dan hapus data klotter & kandang/blok',
                        'default' => false,
                    ],
                    'feature_master_standards' => [
                        'label' => 'Standar Produksi & Pakan',
                        'desc' => 'Konfigurasi target HD, berat telur, dan FCR mingguan',
                        'default' => false,
                    ],
                    'feature_master_settings' => [
                        'label' => 'Pengaturan Farm & Obat',
                        'desc' => 'Pengaturan parameter sistem dan daftar obat',
                        'default' => false,
                    ],
                    'feature_master_permissions' => [
                        'label' => 'Manajemen Hak Akses & User',
                        'desc' => 'Mengatur toggle hak akses menu/fitur seluruh user (Admin Only)',
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

        // Pengguna non-aktif tidak memiliki akses
        if (!$user->is_active) {
            return false;
        }

        // Role 'admin' selalu memiliki akses penuh (Full Access)
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
