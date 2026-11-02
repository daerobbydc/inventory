<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\StockOpnameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display the main audit & inventory reports dashboard.
     */
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang mengakses laporan audit aset.');

        $assets = Asset::with(['category', 'room.building.campus'])->get();

        // 1. Depreciation & Valuation calculation
        $totalAcquisition = $assets->sum('purchase_cost');
        $totalBookValue = $assets->sum(fn ($a) => $a->currentBookValue());
        $totalDepreciation = $totalAcquisition - $totalBookValue;

        // Group by Category
        $byCategory = Category::withCount('assets')->get()->map(function ($cat) {
            $catAssets = Asset::where('category_id', $cat->id)->get();
            $acquisition = $catAssets->sum('purchase_cost');
            $bookValue = $catAssets->sum(fn ($a) => $a->currentBookValue());

            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'code' => $cat->code,
                'count' => $cat->assets_count,
                'acquisition_value' => $acquisition,
                'depreciation_value' => $acquisition - $bookValue,
                'book_value' => $bookValue,
            ];
        });

        // Group by Building / Faculty
        $byBuilding = Building::withCount('rooms')->get()->map(function ($bld) {
            $bldAssets = Asset::whereHas('room', fn ($q) => $q->where('building_id', $bld->id))->get();
            $acquisition = $bldAssets->sum('purchase_cost');
            $bookValue = $bldAssets->sum(fn ($a) => $a->currentBookValue());

            return [
                'id' => $bld->id,
                'name' => $bld->name,
                'code' => $bld->code,
                'room_count' => $bld->rooms_count,
                'asset_count' => $bldAssets->count(),
                'acquisition_value' => $acquisition,
                'book_value' => $bookValue,
            ];
        });

        // 2. Physical Condition & Sensus Aset Yayasan
        $conditionStats = [
            'good' => Asset::where('condition', AssetCondition::Good)->count(),
            'slightly_damaged' => Asset::where('condition', AssetCondition::LightDamage)->count(),
            'damaged' => Asset::where('condition', AssetCondition::HeavyDamage)->count(),
        ];

        $statusStats = [
            'available' => Asset::where('status', AssetStatus::Available)->count(),
            'borrowed' => Asset::where('status', AssetStatus::Borrowed)->count(),
            'maintenance' => Asset::where('status', AssetStatus::Maintenance)->count(),
            'disposed' => Asset::where('status', AssetStatus::Disposed)->count(),
        ];

        // Stock Opname Sessions
        $opnameSessions = StockOpnameSession::with(['createdBy', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Movements
        $recentMovements = AssetMovement::with(['asset', 'fromRoom.building', 'toRoom.building', 'movedBy'])
            ->latest('movement_date')
            ->take(10)
            ->get();

        return Inertia::render('Reports/Index', [
            'valuation' => [
                'total_acquisition' => $totalAcquisition,
                'total_depreciation' => $totalDepreciation,
                'total_book_value' => $totalBookValue,
                'total_assets' => $assets->count(),
            ],
            'byCategory' => $byCategory,
            'byBuilding' => $byBuilding,
            'conditionStats' => $conditionStats,
            'statusStats' => $statusStats,
            'opnameSessions' => $opnameSessions,
            'recentMovements' => $recentMovements,
            'activeCampuses' => Campus::withCount('buildings')->get(),
        ]);
    }

    /**
     * Official Printable Asset Report format.
     */
    public function print(Request $request): Response
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang mencetak laporan buku induk aset.');

        $assets = Asset::with(['category', 'room.building.campus'])->orderBy('category_id')->get();

        $totalAcquisition = $assets->sum('purchase_cost');
        $totalBookValue = $assets->sum(fn ($a) => $a->currentBookValue());

        return Inertia::render('Reports/Print', [
            'assets' => $assets->map(fn ($a) => [
                'id' => $a->id,
                'asset_code' => $a->asset_code,
                'name' => $a->name,
                'category' => $a->category?->name,
                'location' => ($a->room?->building?->name ?? '-').' / '.($a->room?->name ?? '-'),
                'purchase_date' => $a->purchase_date?->format('d/m/Y'),
                'purchase_price' => $a->purchase_cost,
                'book_value' => $a->currentBookValue(),
                'condition' => $a->condition->label(),
                'status' => $a->status->label(),
            ]),
            'summary' => [
                'total_items' => $assets->count(),
                'total_acquisition' => $totalAcquisition,
                'total_book_value' => $totalBookValue,
                'total_depreciation' => $totalAcquisition - $totalBookValue,
                'generated_at' => now()->translatedFormat('d F Y H:i').' WIB',
            ],
        ]);
    }
}
