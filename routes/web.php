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

    // Aset Tetap
    Route::resource('assets', AssetController::class);

    // Barang Habis Pakai / ATK
    Route::resource('consumables', ConsumableController::class)->only(['index', 'store', 'update']);
    Route::post('consumables/{consumable}/transaction', [ConsumableController::class, 'transaction'])->name('consumables.transaction');

    // Peminjaman & Pengembalian Aset
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/{loan}/handover', [LoanController::class, 'handover'])->name('loans.handover');
    Route::post('loans/{loan}/return', [LoanController::class, 'returnItems'])->name('loans.return');

    // Pemeliharaan & Servis
    Route::resource('maintenance', MaintenanceTicketController::class)->except(['destroy']);

    // Sensus BMN & Scanner Kamera Langsung
    Route::get('stock-opname/scanner', [StockOpnameController::class, 'scanner'])->name('stock-opname.scanner');
    Route::get('stock-opname/lookup', [StockOpnameController::class, 'lookup'])->name('stock-opname.lookup');
    Route::post('stock-opname/verify', [StockOpnameController::class, 'verify'])->name('stock-opname.verify');

    // Penghapusan & Pemutihan BMN
    Route::resource('disposals', AssetDisposalController::class)->only(['index', 'store']);
    Route::get('disposals/{asset}/print', [AssetDisposalController::class, 'print'])->name('disposals.print');

    // Laporan & Audit BMN
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');

    // Pengaturan Sistem & Master Data
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Manajemen Pengguna & WhatsApp Gateway
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/test-whatsapp', [UserController::class, 'testWhatsApp'])->name('users.test-whatsapp');

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
