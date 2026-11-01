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
     * Display the main system dashboard with live analytics.
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

        $recentAssets = Asset::with(['category', 'room.building'])
            ->latest()
            ->take(5)
            ->get();

        $recentLoans = LoanTransaction::with(['borrower', 'loanItems.asset'])
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = MaintenanceTicket::with(['asset', 'reporter', 'technician'])
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

        return Inertia::render('Dashboard', [
            'metrics' => [
                'totalAssets' => $totalAssetsCount,
                'totalValuation' => $totalAssetValuation,
                'activeLoans' => $activeLoansCount,
                'activeTickets' => $activeTicketsCount,
                'lowStockCount' => $lowStockConsumables->count(),
            ],
            'lowStockItems' => $lowStockConsumables,
            'recentAssets' => $recentAssets,
            'recentLoans' => $recentLoans,
            'recentTickets' => $recentTickets,
            'conditionCounts' => $conditionCounts,
            'statusCounts' => $statusCounts,
        ]);
    }
}
