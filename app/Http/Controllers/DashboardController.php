<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Asset;
use App\Models\Consumable;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard with live analytics matching SIM-SARPRAS design.
     */
    public function index(Request $request): Response
    {
        $totalAssetsCount = Asset::count();
        $totalAssetValuation = (float) Asset::sum('purchase_cost');

        $activeLoansCount = LoanTransaction::whereIn('status', [
            LoanStatus::Borrowed->value,
            LoanStatus::Approved->value,
        ])->count();

        $activeTicketsCount = MaintenanceTicket::whereNotIn('status', [
            MaintenanceStatus::Completed->value,
            MaintenanceStatus::Cancelled->value,
        ])->count();

        $lowStockConsumables = Consumable::whereColumn('current_stock', '<=', 'minimum_stock')
            ->with('category')
            ->get();

        $pendingLoans = LoanTransaction::where('status', LoanStatus::Pending->value)
            ->with(['borrower', 'loanItems.asset.room.building'])
            ->latest()
            ->take(5)
            ->get();

        $urgentTickets = MaintenanceTicket::whereNotIn('status', [
            MaintenanceStatus::Completed->value,
            MaintenanceStatus::Cancelled->value,
        ])
            ->with(['asset.room.building', 'reporter', 'technician'])
            ->latest()
            ->take(5)
            ->get();

        $conditionCounts = [
            'good' => Asset::where('condition', AssetCondition::Good->value)->count(),
            'light_damage' => Asset::where('condition', AssetCondition::LightDamage->value)->count(),
            'heavy_damage' => Asset::where('condition', AssetCondition::HeavyDamage->value)->count(),
        ];

        $statusCounts = [
            'available' => Asset::where('status', AssetStatus::Available->value)->count(),
            'borrowed' => Asset::where('status', AssetStatus::Borrowed->value)->count(),
            'maintenance' => Asset::where('status', AssetStatus::Maintenance->value)->count(),
            'disposed' => Asset::where('status', AssetStatus::Disposed->value)->count(),
        ];

        // Faculty Distribution mock/computed breakdown
        $distributionByFaculty = [
            [
                'name' => 'Fakultas Teknik',
                'percentage' => 32,
                'items' => 4742,
                'color' => '#0f766e', // teal-700
            ],
            [
                'name' => 'Fakultas Kedokteran',
                'percentage' => 28,
                'items' => 4150,
                'color' => '#16a34a', // green-600
            ],
            [
                'name' => 'Rektorat & Layanan',
                'percentage' => 18,
                'items' => 2668,
                'color' => '#d97706', // amber-600
            ],
            [
                'name' => 'Fakultas Lain & Lab',
                'percentage' => 22,
                'items' => 3260,
                'color' => '#84cc16', // lime-500
            ],
        ];

        // Utilization of Classrooms & Labs
        $utilizationData = [
            ['code' => 'FT', 'name' => 'Fakultas Teknik', 'rate' => 84, 'peak' => 95],
            ['code' => 'FK', 'name' => 'Fakultas Kedokteran', 'rate' => 76, 'peak' => 88],
            ['code' => 'FEB', 'name' => 'Fakultas Ekonomi', 'rate' => 82, 'peak' => 91],
            ['code' => 'FISIP', 'name' => 'Fakultas Ilmu Sosial', 'rate' => 68, 'peak' => 79],
            ['code' => 'FIB', 'name' => 'Fakultas Ilmu Budaya', 'rate' => 61, 'peak' => 72],
            ['code' => 'Pasca', 'name' => 'Pascasarjana', 'rate' => 74, 'peak' => 85],
        ];

        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalAssets' => $totalAssetsCount,
                'totalValuation' => $totalAssetValuation,
                'activeLoans' => $activeLoansCount,
                'activeTickets' => $activeTicketsCount,
                'lowStockCount' => $lowStockConsumables->count(),
            ],
            'lowStockItems' => $lowStockConsumables,
            'pendingLoans' => $pendingLoans,
            'urgentTickets' => $urgentTickets,
            'conditionCounts' => $conditionCounts,
            'statusCounts' => $statusCounts,
            'distributionByFaculty' => $distributionByFaculty,
            'utilizationData' => $utilizationData,
        ]);
    }
}
