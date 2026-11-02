<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Category;
use App\Models\Room;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    /**
     * Display a paginated listing of campus assets with search and filtering.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $roomId = $request->input('room_id');
        $condition = $request->input('condition');
        $status = $request->input('status');

        $assets = Asset::query()
            ->with(['category', 'room.building', 'vendor'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('asset_code', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($roomId, fn ($q) => $q->where('room_id', $roomId))
            ->when($condition, fn ($q) => $q->where('condition', $condition))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'filters' => [
                'search' => $search,
                'category_id' => $categoryId,
                'room_id' => $roomId,
                'condition' => $condition,
                'status' => $status,
            ],
            'categories' => Category::where('is_consumable', false)->orderBy('name')->get(['id', 'name', 'code']),
            'rooms' => Room::with('building')->orderBy('name')->get(['id', 'building_id', 'name', 'code']),
            'conditions' => collect(AssetCondition::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()]),
            'statuses' => collect(AssetStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    /**
     * Show the form for creating a new asset.
     */
    public function create(Request $request): Response
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang mendaftarkan aset baru.');

        $lastId = (Asset::max('id') ?? 0) + 1;
        $suggestedCode = 'AST/'.date('Y').'/'.str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

        return Inertia::render('Assets/Create', [
            'suggestedCode' => $suggestedCode,
            'categories' => Category::where('is_consumable', false)->orderBy('name')->get(['id', 'name']),
            'rooms' => Room::with('building')->orderBy('name')->get(['id', 'building_id', 'name', 'code']),
            'vendors' => Vendor::orderBy('name')->get(['id', 'name']),
            'conditions' => collect(AssetCondition::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()]),
            'statuses' => collect(AssetStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    /**
     * Store a newly created asset in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang mendaftarkan aset baru.');

        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255', 'unique:assets,asset_code'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:50'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['required', 'string'],
            'status' => ['required', 'string'],
            'specifications' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['qr_code_token'] = 'QR-'.Str::upper(Str::random(12));
        $validated['salvage_value'] = $validated['salvage_value'] ?? 0;

        $asset = Asset::create($validated);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Aset baru berhasil didaftarkan ke sistem.');
    }

    /**
     * Display the specified asset.
     */
    public function show(Asset $asset): Response
    {
        $asset->load([
            'category',
            'room.building.campus',
            'vendor',
            'movements.fromRoom',
            'movements.toRoom',
            'movements.movedBy',
            'loanItems.loanTransaction.borrower',
            'maintenanceTickets.reporter',
            'maintenanceTickets.technician',
        ]);

        return Inertia::render('Assets/Show', [
            'asset' => $asset,
            'currentBookValue' => $asset->currentBookValue(),
        ]);
    }

    /**
     * Show the form for editing the specified asset.
     */
    public function edit(Request $request, Asset $asset): Response
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang mengedit data aset.');

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'categories' => Category::where('is_consumable', false)->orderBy('name')->get(['id', 'name']),
            'rooms' => Room::with('building')->orderBy('name')->get(['id', 'building_id', 'name', 'code']),
            'vendors' => Vendor::orderBy('name')->get(['id', 'name']),
            'conditions' => collect(AssetCondition::cases())->map(fn ($c) => ['value' => $c->value, 'label' => $c->label()]),
            'statuses' => collect(AssetStatus::cases())->map(fn ($s) => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    /**
     * Update the specified asset in storage.
     */
    public function update(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang memperbarui data aset.');

        $validated = $request->validate([
            'asset_code' => ['required', 'string', 'max:255', 'unique:assets,asset_code,'.$asset->id],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'vendor_id' => ['nullable', 'exists:vendors,id'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:50'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['required', 'string'],
            'status' => ['required', 'string'],
            'specifications' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldRoomId = $asset->room_id;
        $newRoomId = (int) $validated['room_id'];

        $asset->update($validated);

        // If room changed, record an Asset Movement automatically
        if ($oldRoomId !== $newRoomId && $request->user()) {
            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_room_id' => $oldRoomId,
                'to_room_id' => $newRoomId,
                'moved_by_id' => $request->user()->id,
                'moved_at' => now(),
                'reason' => 'Perubahan lokasi ruangan via sistem',
            ]);
        }

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Informasi aset berhasil diperbarui.');
    }

    /**
     * Remove the specified asset from storage.
     */
    public function destroy(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->canManageAssets(), 403, 'Anda tidak berwenang menghapus aset.');

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus dari inventaris.');
    }
}
