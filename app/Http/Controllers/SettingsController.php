<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Room;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /**
     * Display settings center and master data overview.
     */
    public function index(Request $request): Response
    {
        $campuses = Campus::withCount('buildings')->get();
        $buildings = Building::with(['campus', 'rooms'])->withCount('rooms')->get();
        $rooms = Room::with(['building.campus', 'pic'])->withCount('assets')->get();
        $categories = Category::withCount('assets')->get();
        $vendors = Vendor::withCount('assets')->get();

        $stats = [
            'total_campuses' => $campuses->count(),
            'total_buildings' => $buildings->count(),
            'total_rooms' => $rooms->count(),
            'total_categories' => $categories->count(),
            'total_vendors' => $vendors->count(),
            'total_users' => User::count(),
        ];

        $syncStatus = [
            'synchronizer_status' => 'Online',
            'gateway' => 'Kemdikbudristek SIMAK-BMN v4.2',
            'last_sync' => now()->subMinutes(25)->translatedFormat('d F Y H:i').' WIB',
            'scheduled_interval' => 'Setiap 6 Jam',
            'endpoint' => 'https://api-bmn.kemdikbud.go.id/v1/sync',
            'sync_health' => '100% Berhasil',
        ];

        return Inertia::render('Settings/Index', [
            'stats' => $stats,
            'campuses' => $campuses,
            'buildings' => $buildings,
            'rooms' => $rooms,
            'categories' => $categories,
            'vendors' => $vendors,
            'syncStatus' => $syncStatus,
        ]);
    }
}

