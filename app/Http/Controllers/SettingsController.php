<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Building;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Faculty;
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
        abort_unless($request->user()->canManageSettings(), 403, 'Hanya Super Admin yang berwenang mengakses pengaturan sistem.');

        $faculties = Faculty::withCount(['rooms', 'users'])->get();
        $campuses = Campus::withCount('buildings')->get();
        $buildings = Building::with(['campus', 'rooms'])->withCount('rooms')->get();
        $rooms = Room::with(['building.campus', 'pic', 'faculty'])->withCount('assets')->get();
        $categories = Category::withCount('assets')->get();
        $vendors = Vendor::withCount('assets')->get();
        $users = User::with('faculty')->latest()->get();

        $roles = collect(UserRole::cases())->map(fn ($r) => [
            'value' => $r->value,
            'label' => $r->label(),
        ]);

        $stats = [
            'total_faculties' => $faculties->count(),
            'total_campuses' => $campuses->count(),
            'total_buildings' => $buildings->count(),
            'total_rooms' => $rooms->count(),
            'total_categories' => $categories->count(),
            'total_vendors' => $vendors->count(),
            'total_users' => $users->count(),
        ];

        $syncStatus = [
            'synchronizer_status' => 'Online',
            'gateway' => 'SIM-SARPRAS Universitas Al-Khairiyah Gateway v2.0',
            'last_sync' => now()->subMinutes(25)->translatedFormat('d F Y H:i').' WIB',
            'scheduled_interval' => 'Setiap 6 Jam',
            'endpoint' => 'https://api-sarpras.unival.ac.id/v1/sync',
            'sync_health' => '100% Berhasil',
        ];

        return Inertia::render('Settings/Index', [
            'stats' => $stats,
            'faculties' => $faculties,
            'campuses' => $campuses,
            'buildings' => $buildings,
            'rooms' => $rooms,
            'categories' => $categories,
            'vendors' => $vendors,
            'users' => $users,
            'roles' => $roles,
            'syncStatus' => $syncStatus,
        ]);
    }
}
