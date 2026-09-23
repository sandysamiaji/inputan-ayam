<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    /**
     * Tampilkan Halaman Riwayat Audit & Data Terhapus
     */
    public function index(Request $request)
    {
        AuditLog::ensureTableExists();

        $user = Auth::user();
        $tab = $request->query('tab', 'semua');
        $module = $request->query('module');
        $selectedUserId = $request->query('user_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('q');

        $query = AuditLog::with(['user', 'restorer'])->latest('created_at')->latest('id');

        // Dapatkan kolom yang tersedia
        $hasModuleCol = \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'module');
        $hasActionCol = \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'action');
        $hasIsRestoredCol = \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'is_restored');

        // Filter berdasarkan Tab
        if ($hasActionCol) {
            if ($tab === 'terhapus') {
                $query->where('action', 'DELETE');
            } elseif ($tab === 'update') {
                $query->where('action', 'UPDATE');
            } elseif ($tab === 'create') {
                $query->where('action', 'CREATE');
            } elseif ($tab === 'restored') {
                $query->where(function ($q) use ($hasIsRestoredCol) {
                    if ($hasIsRestoredCol) {
                        $q->where('is_restored', true)->orWhere('action', 'RESTORE');
                    } else {
                        $q->where('action', 'RESTORE');
                    }
                });
            }
        }

        // Filter berdasarkan Modul
        if (!empty($module) && $module !== 'semua') {
            if ($hasModuleCol) {
                if ($module === 'telur') {
                    $query->where(function ($q) {
                        $q->where('module', 'telur')
                          ->orWhere(function ($sub) {
                              $sub->where('module', 'gudang')->where('description', 'like', '%telur%');
                          });
                    });
                } elseif ($module === 'pakan') {
                    $query->where(function ($q) {
                        $q->where('module', 'pakan')
                          ->orWhere(function ($sub) {
                              $sub->where('module', 'gudang')->where('description', 'like', '%pakan%');
                          });
                    });
                } elseif ($module === 'obat') {
                    $query->where(function ($q) {
                        $q->where('module', 'obat')
                          ->orWhere(function ($sub) {
                              $sub->where('module', 'gudang')->where(function ($s) {
                                  $s->where('description', 'like', '%obat%')
                                    ->orWhere('description', 'like', '%vaksin%')
                                    ->orWhere('description', 'like', '%vitamin%');
                              });
                          });
                    });
                } else {
                    $query->where('module', $module);
                }
            }
        }

        // Filter berdasarkan Pengguna
        if (!empty($selectedUserId) && \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'user_id')) {
            $query->where('user_id', $selectedUserId);
        }

        // Filter berdasarkan Rentang Tanggal
        if (\Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'created_at')) {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            } elseif (!empty($startDate)) {
                $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            } elseif (!empty($endDate)) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }
        }

        // Filter Pencarian Kata Kunci
        if (!empty($search)) {
            $s = trim($search);
            $query->where(function ($q) use ($s, $hasActionCol) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%")
                  ->orWhere('table_name', 'like', "%{$s}%");
                if ($hasActionCol) {
                    $q->orWhere('action', 'like', "%{$s}%");
                }
            });
        }

        $items = $query->paginate(15)->withQueryString();

        // Ringkasan Statistik
        $totalAktivitas = AuditLog::count();
        $aktivitasHariIni = \Illuminate\Support\Facades\Schema::hasColumn('audit_logs', 'created_at')
            ? AuditLog::whereDate('created_at', Carbon::today())->count()
            : $totalAktivitas;
        $totalTerhapus = $hasActionCol ? AuditLog::where('action', 'DELETE')->count() : 0;
        $totalTerhapusBelumRestore = ($hasActionCol && $hasIsRestoredCol) 
            ? AuditLog::where('action', 'DELETE')->where('is_restored', false)->count() 
            : 0;
        $totalRestored = $hasIsRestoredCol ? AuditLog::where('is_restored', true)->count() : 0;

        // Daftar Pengguna untuk Filter Dropdown
        $usersList = User::select('id', 'name', 'username', 'role')->orderBy('name')->get();

        // Izin Restore (Admin atau role dengan permission feature_audit_restore)
        $canRestore = $user && ($user->role === 'admin' || $user->canAccess('feature_audit_restore'));

        return view('master.audit', compact(
            'user',
            'items',
            'tab',
            'module',
            'selectedUserId',
            'startDate',
            'endDate',
            'search',
            'totalAktivitas',
            'aktivitasHariIni',
            'totalTerhapus',
            'totalTerhapusBelumRestore',
            'totalRestored',
            'usersList',
            'canRestore'
        ));
    }

    /**
     * Ambil detail data audit dalam format JSON untuk modal snapshot
     */
    public function show($id)
    {
        AuditLog::ensureTableExists();

        $log = AuditLog::with(['user', 'restorer'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'action' => $log->action,
                'module' => $log->module,
                'description' => $log->description,
                'user_name' => $log->user_name,
                'user_role' => $log->user_role,
                'date_formatted' => $log->created_at ? $log->created_at->translatedFormat('d F Y, H:i:s') : '-',
                'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : '-',
                'table_name' => $log->table_name,
                'model_type' => $log->model_type,
                'model_id' => $log->model_id,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'original_data' => $log->original_data,
                'changes' => $log->changes,
                'is_restored' => $log->is_restored,
                'restored_at' => $log->restored_at ? $log->restored_at->translatedFormat('d F Y, H:i:s') : null,
                'restored_by_name' => $log->restored_by_name,
            ]
        ]);
    }

    /**
     * Memulihkan / Me-restore Data yang Telah Dihapus
     */
    public function restore(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || ($user->role !== 'admin' && !$user->canAccess('feature_audit_restore'))) {
            return back()->with('error', 'Akses Ditolak: Anda tidak memiliki izin khusus untuk memulihkan data.');
        }

        $result = AuditService::restoreRecord((int) $id, $user);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
