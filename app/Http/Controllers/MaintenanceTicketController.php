<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\MaintenanceTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceTicketController extends Controller
{
    /**
     * Display a listing of maintenance tickets with filters and metrics.
     */
    public function index(Request $request): Response
    {
        $statusFilter = $request->query('status');
        $priorityFilter = $request->query('priority');
        $search = $request->query('search');

        $query = MaintenanceTicket::with([
            'asset.room.building',
            'reporter',
            'technician',
        ])->latest();

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($priorityFilter && $priorityFilter !== 'all') {
            $query->where('priority', $priorityFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('issue_description', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQ) use ($search) {
                        $assetQ->where('name', 'like', "%{$search}%")
                            ->orWhere('asset_code', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->paginate(10)->withQueryString();

        // Summary metrics
        $metrics = [
            'total' => MaintenanceTicket::count(),
            'reported' => MaintenanceTicket::where('status', MaintenanceStatus::Reported)->count(),
            'in_progress' => MaintenanceTicket::where('status', MaintenanceStatus::InProgress)->count(),
            'waiting_parts' => MaintenanceTicket::where('status', MaintenanceStatus::WaitingParts)->count(),
            'completed' => MaintenanceTicket::where('status', MaintenanceStatus::Completed)->count(),
            'critical' => MaintenanceTicket::where('priority', MaintenancePriority::Critical)
                ->where('status', '!=', MaintenanceStatus::Completed)
                ->count(),
        ];

        // Available technicians for quick dispatch modal
        $technicians = User::where('role', UserRole::Teknisi)
            ->where('is_active', true)
            ->select('id', 'name', 'department', 'phone')
            ->get();

        return Inertia::render('Maintenance/Index', [
            'tickets' => $tickets,
            'metrics' => $metrics,
            'filters' => [
                'status' => $statusFilter ?? 'all',
                'priority' => $priorityFilter ?? 'all',
                'search' => $search ?? '',
            ],
            'technicians' => $technicians,
            'statuses' => collect(MaintenanceStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'priorities' => collect(MaintenancePriority::cases())->map(fn ($p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new maintenance ticket.
     */
    public function create(): Response
    {
        $assets = Asset::with('room.building')
            ->select('id', 'asset_code', 'name', 'room_id', 'condition', 'status')
            ->orderBy('name')
            ->get();

        $technicians = User::where('role', UserRole::Teknisi)
            ->where('is_active', true)
            ->select('id', 'name', 'department', 'phone')
            ->get();

        return Inertia::render('Maintenance/Create', [
            'assets' => $assets,
            'technicians' => $technicians,
            'priorities' => collect(MaintenancePriority::cases())->map(fn ($p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
        ]);
    }

    /**
     * Store a newly created maintenance ticket.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'issue_description' => ['required', 'string', 'min:5'],
            'priority' => ['required', 'string'],
            'technician_id' => ['nullable', 'exists:users,id'],
        ]);

        $ticketCode = 'TCK/'.now()->format('Ym').'/'.strtoupper(Str::random(5));

        $ticket = MaintenanceTicket::create([
            'ticket_code' => $ticketCode,
            'asset_id' => $validated['asset_id'],
            'reporter_id' => $request->user()->id,
            'technician_id' => $validated['technician_id'] ?? null,
            'issue_description' => $validated['issue_description'],
            'priority' => $validated['priority'],
            'status' => MaintenanceStatus::Reported,
        ]);

        // Update asset status to Maintenance
        $asset = Asset::find($validated['asset_id']);
        if ($asset) {
            $condition = match ($validated['priority']) {
                MaintenancePriority::Critical->value => AssetCondition::HeavyDamage,
                MaintenancePriority::High->value => AssetCondition::HeavyDamage,
                default => AssetCondition::LightDamage,
            };

            $asset->update([
                'status' => AssetStatus::Maintenance,
                'condition' => $condition,
            ]);
        }

        return redirect()->route('maintenance.show', $ticket)
            ->with('success', "Tiket perbaikan {$ticket->ticket_code} berhasil dibuat dan diteruskan ke tim teknisi.");
    }

    /**
     * Display the specified maintenance ticket.
     */
    public function show(MaintenanceTicket $maintenance): Response
    {
        $maintenance->load([
            'asset.category',
            'asset.room.building.campus',
            'reporter',
            'technician',
        ]);

        $technicians = User::where('role', UserRole::Teknisi)
            ->where('is_active', true)
            ->select('id', 'name', 'department', 'phone')
            ->get();

        return Inertia::render('Maintenance/Show', [
            'ticket' => $maintenance,
            'technicians' => $technicians,
            'statuses' => collect(MaintenanceStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
            'priorities' => collect(MaintenancePriority::cases())->map(fn ($p) => [
                'value' => $p->value,
                'label' => $p->label(),
            ]),
        ]);
    }

    /**
     * Update ticket progress, technician assignment, and costs.
     */
    public function update(Request $request, MaintenanceTicket $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'technician_id' => ['nullable', 'exists:users,id'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $isCompleting = $validated['status'] === MaintenanceStatus::Completed->value;

        $maintenance->update([
            'status' => $validated['status'],
            'technician_id' => $validated['technician_id'] ?? $maintenance->technician_id,
            'cost' => $validated['cost'] ?? $maintenance->cost,
            'resolution_notes' => $validated['resolution_notes'] ?? $maintenance->resolution_notes,
            'completed_at' => $isCompleting ? now() : $maintenance->completed_at,
        ]);

        // If completed, return asset to Available & Good condition
        if ($isCompleting && $maintenance->asset) {
            $maintenance->asset->update([
                'status' => AssetStatus::Available,
                'condition' => AssetCondition::Good,
            ]);
        }

        return back()->with('success', "Status tiket {$maintenance->ticket_code} berhasil diperbarui.");
    }
}
