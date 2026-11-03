<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\ProcurementStatus;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Faculty;
use App\Models\ProcurementItem;
use App\Models\ProcurementRequest;
use App\Models\Room;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProcurementController extends Controller
{
    /**
     * Display a listing of procurement proposals.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = ProcurementRequest::with([
            'faculty',
            'user',
            'approver',
            'vendor',
            'items.category',
            'items.targetRoom',
        ]);

        // Faculty Isolation: Non-Super Admin sees only their faculty's proposals
        if (! $user->isSuperAdmin()) {
            if ($user->faculty_id) {
                $query->where('faculty_id', $user->faculty_id);
            } else {
                $query->where('user_id', $user->id);
            }
        } elseif ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->input('faculty_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->input('urgency'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('request_number', 'like', "%{$search}%")
                    ->orWhere('fiscal_year', 'like', "%{$search}%");
            });
        }

        // Summary counts
        $baseQuery = ProcurementRequest::query();
        if (! $user->isSuperAdmin()) {
            if ($user->faculty_id) {
                $baseQuery->where('faculty_id', $user->faculty_id);
            } else {
                $baseQuery->where('user_id', $user->id);
            }
        }

        $metrics = [
            'totalRequests' => (clone $baseQuery)->count(),
            'pendingReview' => (clone $baseQuery)->whereIn('status', [
                ProcurementStatus::Submitted->value,
                ProcurementStatus::UnderReview->value,
            ])->count(),
            'approvedCount' => (clone $baseQuery)->whereIn('status', [
                ProcurementStatus::Approved->value,
                ProcurementStatus::Ordered->value,
                ProcurementStatus::Received->value,
            ])->count(),
            'totalApprovedBudget' => (float) (clone $baseQuery)->whereIn('status', [
                ProcurementStatus::Approved->value,
                ProcurementStatus::Ordered->value,
                ProcurementStatus::Received->value,
                ProcurementStatus::Completed->value,
            ])->sum('total_estimated_cost'),
        ];

        $procurements = $query->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Procurements/Index', [
            'procurements' => $procurements,
            'metrics' => $metrics,
            'filters' => $request->only(['search', 'status', 'urgency', 'faculty_id']),
            'faculties' => Faculty::select('id', 'name', 'code')->get(),
            'statuses' => collect(ProcurementStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
                'badgeClass' => $s->badgeClass(),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new procurement proposal.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user->canCreateProcurement(), 403, 'Hanya Staff / Pengelola Fakultas yang dapat mengajukan usulan pengadaan.');

        $faculties = $user->isSuperAdmin()
            ? Faculty::select('id', 'name', 'code')->get()
            : Faculty::where('id', $user->faculty_id)->select('id', 'name', 'code')->get();

        $rooms = Room::with('building')
            ->when(! $user->isSuperAdmin() && $user->faculty_id, fn ($q) => $q->where('faculty_id', $user->faculty_id))
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => "{$r->name} ({$r->building?->name})",
                'code' => $r->code,
            ]);

        $defaultFaculty = $user->faculty_id ? Faculty::find($user->faculty_id) : $faculties->first();
        $suggestedNumber = ProcurementRequest::generateRequestNumber($defaultFaculty);

        return Inertia::render('Procurements/Create', [
            'faculties' => $faculties,
            'categories' => Category::select('id', 'name', 'code')->get(),
            'rooms' => $rooms,
            'suggestedNumber' => $suggestedNumber,
            'userFacultyId' => $user->faculty_id,
        ]);
    }

    /**
     * Store a newly created procurement proposal in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canCreateProcurement(), 403, 'Hanya Staff / Pengelola Fakultas yang dapat mengajukan usulan pengadaan.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'fiscal_year' => ['required', 'string', 'max:20'],
            'budget_source' => ['required', 'string', 'max:255'],
            'urgency' => ['required', 'in:reguler,urgent,critical'],
            'justification' => ['required', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.category_id' => ['nullable', 'exists:categories,id'],
            'items.*.brand' => ['nullable', 'string', 'max:255'],
            'items.*.model' => ['nullable', 'string', 'max:255'],
            'items.*.specifications' => ['nullable', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price_estimate' => ['required', 'numeric', 'min:0'],
            'items.*.target_room_id' => ['nullable', 'exists:rooms,id'],
        ]);

        // Enforce faculty ownership for non-super admins
        $facultyId = $user->isSuperAdmin()
            ? ($validated['faculty_id'] ?? $user->faculty_id)
            : $user->faculty_id;

        $faculty = $facultyId ? Faculty::find($facultyId) : null;
        $requestNumber = ProcurementRequest::generateRequestNumber($faculty);

        $totalEstimatedCost = collect($validated['items'])->sum(function ($item) {
            return (int) $item['quantity'] * (float) $item['unit_price_estimate'];
        });

        DB::transaction(function () use ($validated, $user, $facultyId, $requestNumber, $totalEstimatedCost) {
            $procurement = ProcurementRequest::create([
                'request_number' => $requestNumber,
                'faculty_id' => $facultyId,
                'user_id' => $user->id,
                'title' => $validated['title'],
                'fiscal_year' => $validated['fiscal_year'],
                'budget_source' => $validated['budget_source'],
                'urgency' => $validated['urgency'],
                'total_estimated_cost' => $totalEstimatedCost,
                'justification' => $validated['justification'],
                'status' => ProcurementStatus::Submitted,
            ]);

            foreach ($validated['items'] as $itemData) {
                $qty = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price_estimate'];

                ProcurementItem::create([
                    'procurement_request_id' => $procurement->id,
                    'category_id' => $itemData['category_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'brand' => $itemData['brand'] ?? null,
                    'model' => $itemData['model'] ?? null,
                    'specifications' => $itemData['specifications'] ?? null,
                    'quantity' => $qty,
                    'unit_price_estimate' => $unitPrice,
                    'total_price_estimate' => $qty * $unitPrice,
                    'target_room_id' => $itemData['target_room_id'] ?? null,
                    'is_converted_to_asset' => false,
                ]);
            }
        });

        return redirect()->route('procurements.index')
            ->with('success', "Usulan pengadaan {$requestNumber} berhasil diajukan ke Biro Sarpras.");
    }

    /**
     * Display the specified procurement proposal.
     */
    public function show(Request $request, ProcurementRequest $procurement): Response
    {
        $user = $request->user();

        // Authorization check
        if (! $user->isSuperAdmin()) {
            if ($user->faculty_id && $procurement->faculty_id !== $user->faculty_id && $procurement->user_id !== $user->id) {
                abort(403, 'Anda tidak berhak melihat usulan pengadaan fakultas lain.');
            }
        }

        $procurement->load([
            'faculty',
            'user',
            'approver',
            'vendor',
            'items.category',
            'items.targetRoom.building',
        ]);

        return Inertia::render('Procurements/Show', [
            'procurement' => $procurement,
            'vendors' => Vendor::select('id', 'name', 'phone')->get(),
            'canApprove' => $user->canManageProcurements(),
            'canEditPurchase' => $user->isStaff(),
        ]);
    }

    /**
     * Update approval status of the procurement proposal (Super Admin / Warek II).
     */
    public function updateStatus(Request $request, ProcurementRequest $procurement): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canManageProcurements(), 403, 'Hanya Biro Sarpras / Super Admin yang berwenang memvalidasi usulan pengadaan.');

        $validated = $request->validate([
            'status' => ['required', 'in:under_review,approved,rejected'],
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $statusEnum = ProcurementStatus::from($validated['status']);

        $procurement->update([
            'status' => $statusEnum,
            'approval_notes' => $validated['approval_notes'],
            'approver_id' => $user->id,
            'approved_at' => now(),
        ]);

        $statusLabel = $statusEnum->label();

        return back()->with('success', "Status usulan pengadaan diperbarui menjadi: {$statusLabel}.");
    }

    /**
     * Update purchasing order and delivery details (PO, Vendor, Receipt date).
     */
    public function updatePurchase(Request $request, ProcurementRequest $procurement): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403, 'Hanya Staff yang berwenang memperbarui status pemesanan pengadaan.');

        $validated = $request->validate([
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'purchase_order_number' => ['nullable', 'string', 'max:100'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'receipt_date' => ['nullable', 'date'],
            'status' => ['required', 'in:ordered,received'],
        ]);

        $procurement->update([
            'vendor_id' => $validated['vendor_id'] ?? $procurement->vendor_id,
            'purchase_order_number' => $validated['purchase_order_number'] ?? $procurement->purchase_order_number,
            'invoice_number' => $validated['invoice_number'] ?? $procurement->invoice_number,
            'receipt_date' => $validated['receipt_date'] ?? $procurement->receipt_date,
            'status' => ProcurementStatus::from($validated['status']),
        ]);

        return back()->with('success', 'Informasi pemesanan & penerimaan fisik berhasil diperbarui.');
    }

    /**
     * 1-Click Convert received procurement items into Master Assets (Asset Catalog).
     */
    public function convertToAssets(Request $request, ProcurementRequest $procurement): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canManageProcurements(), 403, 'Hanya Biro Sarpras / Super Admin yang berwenang mendaftarkan aset ke buku induk.');

        // Allow conversion if status is received or approved/ordered
        if (! in_array($procurement->status, [ProcurementStatus::Received, ProcurementStatus::Approved, ProcurementStatus::Ordered], true)) {
            return back()->with('error', 'Usulan pengadaan harus disetujui atau barang telah diterima fisik sebelum dikonversi ke aset.');
        }

        $procurement->load(['items', 'faculty']);

        $totalAssetsCreated = 0;

        DB::transaction(function () use ($procurement, &$totalAssetsCreated) {
            $year = date('Y');
            $facCode = $procurement->faculty?->code ?: 'UNIV';

            foreach ($procurement->items as $item) {
                if ($item->is_converted_to_asset) {
                    continue;
                }

                // Fallback default room if item target_room_id is null
                $targetRoomId = $item->target_room_id ?: Room::where('faculty_id', $procurement->faculty_id)->value('id') ?: Room::value('id');

                // Fallback category if null
                $categoryId = $item->category_id ?: Category::value('id');

                for ($i = 1; $i <= $item->quantity; $i++) {
                    $randomToken = Str::upper(Str::random(4));
                    $assetCode = "AST/{$facCode}/{$year}/{$item->id}-{$i}-{$randomToken}";

                    Asset::create([
                        'asset_code' => $assetCode,
                        'name' => $item->item_name.($item->quantity > 1 ? " (#{$i})" : ''),
                        'brand' => $item->brand,
                        'model' => $item->model,
                        'category_id' => $categoryId,
                        'room_id' => $targetRoomId,
                        'vendor_id' => $procurement->vendor_id,
                        'purchase_date' => $procurement->receipt_date ?: now(),
                        'purchase_cost' => $item->unit_price_estimate,
                        'useful_life_years' => 5,
                        'salvage_value' => 0,
                        'condition' => AssetCondition::Good,
                        'status' => AssetStatus::Available,
                        'qr_code_token' => 'QR-'.Str::upper(Str::random(12)),
                        'specifications' => $item->specifications,
                        'notes' => "Pengadaan resmi dari usulan: {$procurement->request_number} ({$procurement->title})",
                    ]);

                    $totalAssetsCreated++;
                }

                $item->update(['is_converted_to_asset' => true]);
            }

            $procurement->update([
                'status' => ProcurementStatus::Completed,
            ]);
        });

        return back()->with('success', "Sukses! {$totalAssetsCreated} unit aset baru telah otomatis diterbitkan ke Katalog & Buku Induk Aset.");
    }

    /**
     * Remove the specified procurement request (Draft / Submitted only).
     */
    public function destroy(Request $request, ProcurementRequest $procurement): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isSuperAdmin() && $procurement->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang menghapus usulan pengadaan ini.');
        }

        if (in_array($procurement->status, [ProcurementStatus::Approved, ProcurementStatus::Completed], true)) {
            return back()->with('error', 'Usulan yang telah disetujui atau telah selesai tidak dapat dihapus.');
        }

        $num = $procurement->request_number;
        $procurement->delete();

        return redirect()->route('procurements.index')
            ->with('success', "Usulan pengadaan {$num} berhasil dihapus.");
    }
}
