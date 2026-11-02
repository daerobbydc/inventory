<?php

namespace App\Http\Controllers;

use App\Enums\RoomBookingStatus;
use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoomBookingController extends Controller
{
    /**
     * Display a listing of room bookings.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $isStaff = $user->canManageRoomBookings();

        $status = $request->input('status');
        $date = $request->input('date');
        $roomId = $request->input('room_id');
        $search = $request->input('search');

        $query = RoomBooking::query()
            ->with(['room.building', 'room.faculty', 'user.faculty', 'approver'])
            ->when(! $isStaff, function ($q) use ($user) {
                // Non-staff view their own reservations
                $q->where('user_id', $user->id);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($date, fn ($q) => $q->whereDate('booking_date', $date))
            ->when($roomId, fn ($q) => $q->where('room_id', $roomId))
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('room', fn ($r) => $r->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('booking_date')
            ->latest('start_time');

        $bookings = $query->paginate(10)->withQueryString();

        // Key metrics
        $metrics = [
            'total' => $isStaff ? RoomBooking::count() : RoomBooking::where('user_id', $user->id)->count(),
            'pending' => $isStaff
                ? RoomBooking::where('status', RoomBookingStatus::Pending->value)->count()
                : RoomBooking::where('user_id', $user->id)->where('status', RoomBookingStatus::Pending->value)->count(),
            'approved_today' => RoomBooking::where('status', RoomBookingStatus::Approved->value)
                ->whereDate('booking_date', now()->toDateString())
                ->count(),
            'active_rooms' => RoomBooking::where('status', RoomBookingStatus::Approved->value)
                ->whereDate('booking_date', now()->toDateString())
                ->distinct('room_id')
                ->count('room_id'),
        ];

        $rooms = Room::with('building')->orderBy('name')->get(['id', 'building_id', 'name', 'code']);

        return Inertia::render('RoomBookings/Index', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $status,
                'date' => $date,
                'room_id' => $roomId,
                'search' => $search,
            ],
            'metrics' => $metrics,
            'rooms' => $rooms,
            'isStaff' => $isStaff,
            'statuses' => collect(RoomBookingStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new room booking.
     */
    public function create(): Response
    {
        $rooms = Room::with(['building.campus', 'faculty', 'pic'])
            ->orderBy('name')
            ->get();

        $today = now()->toDateString();
        $todaySchedule = RoomBooking::with(['room', 'user'])
            ->whereDate('booking_date', $today)
            ->whereIn('status', [RoomBookingStatus::Approved->value, RoomBookingStatus::Pending->value])
            ->orderBy('start_time')
            ->get();

        return Inertia::render('RoomBookings/Create', [
            'rooms' => $rooms,
            'today' => $today,
            'todaySchedule' => $todaySchedule,
        ]);
    }

    /**
     * Store a newly created room booking in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'purpose' => ['required', 'string', 'max:1000'],
            'attendees_count' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        // Anti-conflict checking
        $hasConflict = RoomBooking::overlapping(
            (int) $validated['room_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $validated['end_time']
        )->exists();

        if ($hasConflict) {
            return back()->withInput()->withErrors([
                'start_time' => 'Ruangan telah dipesan atau diajukan pada rentang jam tersebut. Silakan pilih ruangan atau slot waktu lain.',
            ]);
        }

        $year = now()->year;
        $count = RoomBooking::whereYear('created_at', $year)->count() + 1;
        $bookingCode = sprintf('RSRV-%d-%04d', $year, $count);

        $booking = RoomBooking::create([
            'booking_code' => $bookingCode,
            'room_id' => $validated['room_id'],
            'user_id' => $request->user()->id,
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'purpose' => $validated['purpose'],
            'attendees_count' => $validated['attendees_count'],
            'status' => RoomBookingStatus::Pending,
        ]);

        return redirect()->route('room-bookings.show', $booking->id)
            ->with('success', 'Permohonan peminjaman ruangan berhasil dikirim. Menunggu verifikasi dari Biro Sarpras / PIC Ruangan.');
    }

    /**
     * Display the specified room booking.
     */
    public function show(Request $request, RoomBooking $roomBooking): Response
    {
        $user = $request->user();
        $canManage = $user->canManageRoomBookings();

        if (! $canManage && $roomBooking->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat rincian peminjaman ruangan milik pengguna lain.');
        }

        $roomBooking->load(['room.building.campus', 'room.pic', 'room.faculty', 'user.faculty', 'approver']);

        return Inertia::render('RoomBookings/Show', [
            'booking' => $roomBooking,
            'canManage' => $canManage,
        ]);
    }

    /**
     * Approve the specified room booking (Staff only).
     */
    public function approve(Request $request, RoomBooking $roomBooking): RedirectResponse
    {
        abort_unless($request->user()->canManageRoomBookings(), 403, 'Hanya Staf Sarpras atau PIC Ruangan yang berhak menyetujui peminjaman ruangan.');

        // Conflict check against other already approved bookings
        $conflict = RoomBooking::where('room_id', $roomBooking->room_id)
            ->whereDate('booking_date', $roomBooking->booking_date)
            ->where('status', RoomBookingStatus::Approved->value)
            ->where('id', '!=', $roomBooking->id)
            ->where(function ($q) use ($roomBooking) {
                $q->where('start_time', '<', $roomBooking->end_time)
                    ->where('end_time', '>', $roomBooking->start_time);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Gagal menyetujui: Ruangan ini telah memiliki jadwal kegiatan lain yang disetujui pada jam tersebut.');
        }

        $roomBooking->update([
            'status' => RoomBookingStatus::Approved,
            'approver_id' => $request->user()->id,
            'approval_notes' => $request->input('approval_notes', 'Permohonan peminjaman ruangan telah diperiksa dan disetujui.'),
        ]);

        return back()->with('success', "Peminjaman ruangan {$roomBooking->room->name} telah resmi disetujui.");
    }

    /**
     * Reject the specified room booking (Staff only).
     */
    public function reject(Request $request, RoomBooking $roomBooking): RedirectResponse
    {
        abort_unless($request->user()->canManageRoomBookings(), 403, 'Hanya Staf Sarpras atau PIC Ruangan yang berhak menolak peminjaman ruangan.');

        $request->validate([
            'approval_notes' => ['required', 'string', 'max:500'],
        ]);

        $roomBooking->update([
            'status' => RoomBookingStatus::Rejected,
            'approver_id' => $request->user()->id,
            'approval_notes' => $request->input('approval_notes'),
        ]);

        return back()->with('success', "Permohonan peminjaman ruangan {$roomBooking->room->name} telah ditolak.");
    }

    /**
     * Cancel the specified room booking by borrower.
     */
    public function cancel(Request $request, RoomBooking $roomBooking): RedirectResponse
    {
        $user = $request->user();

        if (! $user->canManageRoomBookings() && $roomBooking->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan permohonan ini.');
        }

        if ($roomBooking->status === RoomBookingStatus::Completed) {
            return back()->with('error', 'Kegiatan peminjaman telah selesai dan tidak dapat dibatalkan.');
        }

        $roomBooking->update([
            'status' => RoomBookingStatus::Cancelled,
            'approval_notes' => 'Dibatalkan oleh pemohon.',
        ]);

        return back()->with('success', 'Permohonan peminjaman ruangan berhasil dibatalkan.');
    }
}
