<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\RoomBookingStatus;
use App\Models\Asset;
use App\Models\Consumable;
use App\Models\Faculty;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display role-specific dashboard for optimal user experience.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // 1. Dashboard Mahasiswa / Dosen / Tendik (Civitas Academic Portal)
        if ($user && $user->isCivitas()) {
            $myLoans = LoanTransaction::where('borrower_id', $user->id)
                ->with(['loanItems.asset.room.building'])
                ->latest()
                ->take(5)
                ->get();

            $activeLoansCount = LoanTransaction::where('borrower_id', $user->id)
                ->whereIn('status', [LoanStatus::Borrowed->value, LoanStatus::Approved->value])
                ->count();

            $myBookings = RoomBooking::where('user_id', $user->id)
                ->with(['room.building', 'room.faculty'])
                ->latest('booking_date')
                ->take(5)
                ->get();

            $activeBookingsCount = RoomBooking::where('user_id', $user->id)
                ->whereIn('status', [RoomBookingStatus::Approved->value, RoomBookingStatus::Pending->value])
                ->count();

            $myTickets = MaintenanceTicket::where('reporter_id', $user->id)
                ->with(['asset.room.building', 'technician'])
                ->latest()
                ->take(5)
                ->get();

            $activeTicketsCount = MaintenanceTicket::where('reporter_id', $user->id)
                ->whereNotIn('status', [MaintenanceStatus::Completed->value, MaintenanceStatus::Cancelled->value])
                ->count();

            $availableRoomsCount = Room::count();

            return Inertia::render('Dashboard', [
                'dashboardType' => 'civitas',
                'civitasData' => [
                    'myLoans' => $myLoans,
                    'activeLoansCount' => $activeLoansCount,
                    'myBookings' => $myBookings,
                    'activeBookingsCount' => $activeBookingsCount,
                    'myTickets' => $myTickets,
                    'activeTicketsCount' => $activeTicketsCount,
                    'availableRoomsCount' => $availableRoomsCount,
                ],
            ]);
        }

        // 2. Dashboard Teknisi (Maintenance & Field Queue)
        if ($user && $user->isTeknisi()) {
            $myAssignedTickets = MaintenanceTicket::where('technician_id', $user->id)
                ->whereNotIn('status', [MaintenanceStatus::Completed->value, MaintenanceStatus::Cancelled->value])
                ->with(['asset.room.building', 'reporter'])
                ->latest()
                ->get();

            $urgentTickets = MaintenanceTicket::whereIn('priority', ['critical', 'high'])
                ->whereNotIn('status', [MaintenanceStatus::Completed->value, MaintenanceStatus::Cancelled->value])
                ->with(['asset.room.building', 'reporter', 'technician'])
                ->latest()
                ->take(5)
                ->get();

            $completedThisMonth = MaintenanceTicket::where('technician_id', $user->id)
                ->where('status', MaintenanceStatus::Completed->value)
                ->whereMonth('completed_at', now()->month)
                ->count();

            $waitingPartsCount = MaintenanceTicket::where('technician_id', $user->id)
                ->where('status', MaintenanceStatus::WaitingParts->value)
                ->count();

            return Inertia::render('Dashboard', [
                'dashboardType' => 'teknisi',
                'teknisiData' => [
                    'myAssignedTickets' => $myAssignedTickets,
                    'urgentTickets' => $urgentTickets,
                    'metrics' => [
                        'assignedCount' => $myAssignedTickets->count(),
                        'urgentCount' => $urgentTickets->count(),
                        'waitingPartsCount' => $waitingPartsCount,
                        'completedThisMonth' => $completedThisMonth,
                    ],
                ],
            ]);
        }

        // 3. Dashboard PIC Ruangan / Laboran
        if ($user && $user->isPicRuangan()) {
            $myRooms = Room::where('pic_user_id', $user->id)
                ->with('building.campus')
                ->withCount('assets')
                ->get();

            $myRoomIds = $myRooms->pluck('id')->all();

            $pendingLoans = LoanTransaction::where('status', LoanStatus::Pending->value)
                ->whereHas('loanItems.asset', fn ($q) => $q->whereIn('room_id', $myRoomIds))
                ->with(['borrower', 'loanItems.asset.room'])
                ->latest()
                ->take(5)
                ->get();

            $pendingBookings = RoomBooking::where('status', RoomBookingStatus::Pending->value)
                ->whereIn('room_id', $myRoomIds)
                ->with(['user', 'room'])
                ->latest()
                ->take(5)
                ->get();

            $todayBookings = RoomBooking::whereIn('room_id', $myRoomIds)
                ->whereDate('booking_date', now()->toDateString())
                ->with(['user', 'room'])
                ->orderBy('start_time')
                ->get();

            $totalAssetsInRooms = Asset::whereIn('room_id', $myRoomIds)->count();
            $damagedAssetsCount = Asset::whereIn('room_id', $myRoomIds)
                ->whereIn('condition', [AssetCondition::LightDamage->value, AssetCondition::HeavyDamage->value])
                ->count();

            return Inertia::render('Dashboard', [
                'dashboardType' => 'pic_ruangan',
                'picData' => [
                    'myRooms' => $myRooms,
                    'pendingLoans' => $pendingLoans,
                    'pendingBookings' => $pendingBookings,
                    'todayBookings' => $todayBookings,
                    'metrics' => [
                        'roomsCount' => $myRooms->count(),
                        'totalAssets' => $totalAssetsInRooms,
                        'pendingApprovals' => $pendingLoans->count() + $pendingBookings->count(),
                        'damagedAssetsCount' => $damagedAssetsCount,
                    ],
                ],
            ]);
        }

        // 4. Dashboard Eksekutif & Biro Sarpras (Super Admin & Admin Fakultas)
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

        $pendingBookings = RoomBooking::where('status', RoomBookingStatus::Pending->value)
            ->with(['user', 'room.building'])
            ->latest()
            ->take(5)
            ->get();

        $recentCirculations = LoanTransaction::with(['borrower', 'loanItems.asset.room.building'])
            ->latest()
            ->take(6)
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

        // Faculty Distribution with real asset counts from database
        $faculties = Faculty::withCount('rooms')->get();
        $totalAssetsCount = Asset::count();

        $distributionByFaculty = $faculties->map(function ($fac) use ($totalAssetsCount) {
            $assetsCount = Asset::whereHas('room', function ($q) use ($fac) {
                $q->where('faculty_id', $fac->id);
            })->count();

            $pct = $totalAssetsCount > 0 ? round(($assetsCount / $totalAssetsCount) * 100) : 0;

            $code = $fac->code ?: strtoupper(substr($fac->name, 0, 4));
            if ($code === 'REKTORAT') {
                $code = 'REK';
            }

            return [
                'id' => $fac->id,
                'code' => $code,
                'name' => $fac->name,
                'rooms_count' => $fac->rooms_count,
                'assets_count' => $assetsCount,
                'items' => $assetsCount,
                'percentage' => $pct,
            ];
        })->values()->all();

        // Weekly activity for room bookings and asset loans (Monday to Sunday)
        $startOfWeek = now()->startOfWeek();
        $weeklyActivity = [];
        $dayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();
            $bookingCount = RoomBooking::whereDate('booking_date', $date)->count();
            $loanCount = LoanTransaction::whereDate('loan_date', $date)->count();
            $weeklyActivity[] = [
                'day' => $dayLabels[$i],
                'date' => $date,
                'total' => $bookingCount + $loanCount,
                'bookings' => $bookingCount,
                'loans' => $loanCount,
            ];
        }

        // Service resolution over the last 7 days
        $serviceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $completedCount = MaintenanceTicket::whereDate('completed_at', $date)->count();
            $createdCount = MaintenanceTicket::whereDate('created_at', $date)->count();
            $serviceTrend[] = [
                'label' => $i === 0 ? 'Hari Ini' : 'H-'.$i,
                'date' => $date,
                'completed' => $completedCount,
                'incoming' => $createdCount,
            ];
        }

        return Inertia::render('Dashboard', [
            'dashboardType' => 'executive',
            'metrics' => [
                'totalAssets' => $totalAssetsCount,
                'totalValuation' => $totalAssetValuation,
                'activeLoans' => $activeLoansCount,
                'activeTickets' => $activeTicketsCount,
                'lowStockCount' => $lowStockConsumables->count(),
            ],
            'lowStockItems' => $lowStockConsumables,
            'pendingLoans' => $pendingLoans,
            'pendingBookings' => $pendingBookings,
            'recentCirculations' => $recentCirculations,
            'urgentTickets' => $urgentTickets,
            'conditionCounts' => $conditionCounts,
            'statusCounts' => $statusCounts,
            'distributionByFaculty' => $distributionByFaculty,
            'utilizationData' => $weeklyActivity,
            'weeklyActivity' => $weeklyActivity,
            'serviceTrend' => $serviceTrend,
        ]);
    }
}
