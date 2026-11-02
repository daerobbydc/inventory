<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\ConsumableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaintenanceTicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Aset Tetap - Modifikasi Aset hanya untuk Staff (Super Admin, Admin Fakultas, PIC Ruangan)
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan'])->group(function () {
        Route::get('assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('assets', [AssetController::class, 'store'])->name('assets.store');
        Route::get('assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });
    // Aset Tetap - Akses Lihat / Katalog untuk semua civitas terverifikasi
    Route::get('assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('assets/{asset}', [AssetController::class, 'show'])->name('assets.show');

    // Barang Habis Pakai / ATK - Mutasi & Tambah hanya untuk Staff
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan'])->group(function () {
        Route::post('consumables', [ConsumableController::class, 'store'])->name('consumables.store');
        Route::put('consumables/{consumable}', [ConsumableController::class, 'update'])->name('consumables.update');
        Route::post('consumables/{consumable}/transaction', [ConsumableController::class, 'transaction'])->name('consumables.transaction');
    });
    Route::get('consumables', [ConsumableController::class, 'index'])->name('consumables.index');

    // Peminjaman & Pengembalian Aset - Verifikasi & Handover hanya untuk Staff
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan'])->group(function () {
        Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/handover', [LoanController::class, 'handover'])->name('loans.handover');
        Route::post('loans/{loan}/return', [LoanController::class, 'returnItems'])->name('loans.return');
    });
    // Peminjaman & Pengembalian Aset - Permohonan & Riwayat oleh Peminjam
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);

    // Pemeliharaan & Servis - Update Status / Dispatch teknisi oleh Staff & Teknisi
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan,teknisi'])->group(function () {
        Route::put('maintenance/{maintenance}', [MaintenanceTicketController::class, 'update'])->name('maintenance.update');
    });
    // Pemeliharaan & Servis - Pelaporan & Monitoring Tiket oleh Semua Civitas
    Route::resource('maintenance', MaintenanceTicketController::class)->only(['index', 'create', 'store', 'show']);

    // Sensus Aset & Scanner Kamera Langsung - Hanya Staff & Teknisi
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan,teknisi'])->group(function () {
        Route::get('stock-opname/scanner', [StockOpnameController::class, 'scanner'])->name('stock-opname.scanner');
        Route::get('stock-opname/lookup', [StockOpnameController::class, 'lookup'])->name('stock-opname.lookup');
        Route::post('stock-opname/verify', [StockOpnameController::class, 'verify'])->name('stock-opname.verify');
    });

    // Penghapusan & Pemutihan Aset Yayasan - Hanya Staff
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan'])->group(function () {
        Route::resource('disposals', AssetDisposalController::class)->only(['index', 'store']);
        Route::get('disposals/{asset}/print', [AssetDisposalController::class, 'print'])->name('disposals.print');
    });

    // Laporan & Audit Aset Yayasan - Hanya Staff
    Route::middleware(['role:super_admin,admin_fakultas,pic_ruangan'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    });

    // Pengaturan Sistem & Master Data - Hanya Super Admin (Biro Sarpras)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('users/test-whatsapp', [UserController::class, 'testWhatsApp'])->name('users.test-whatsapp');
    });

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
