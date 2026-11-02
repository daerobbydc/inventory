<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssetDisposalController extends Controller
{
    /**
     * Display listing of disposed assets and pending disposal proposals.
     */
    public function index(Request $request): Response
    {
        abort_unless($request->user()->canManageDisposals(), 403, 'Anda tidak berwenang mengelola penghapusan aset.');

        $disposedAssets = Asset::with(['category', 'room.building.campus'])
            ->where('status', AssetStatus::Disposed)
            ->latest('updated_at')
            ->paginate(10);

        // Assets eligible for disposal: Heavy Damage or Book Value <= 0
        $eligibleAssets = Asset::with(['category', 'room.building'])
            ->where('status', '!=', AssetStatus::Disposed)
            ->where(function ($q) {
                $q->where('condition', AssetCondition::HeavyDamage)
                    ->orWhere('status', AssetStatus::Maintenance);
            })
            ->get();

        $metrics = [
            'total_disposed' => Asset::where('status', AssetStatus::Disposed)->count(),
            'eligible_count' => $eligibleAssets->count(),
            'total_salvage_recovered' => Asset::where('status', AssetStatus::Disposed)->sum('salvage_value') ?? 0,
        ];

        return Inertia::render('Disposals/Index', [
            'disposedAssets' => $disposedAssets,
            'eligibleAssets' => $eligibleAssets,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Process asset disposal proposal and generate Berita Acara.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageDisposals(), 403, 'Anda tidak berwenang memproses penghapusan aset.');

        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'disposal_method' => ['required', 'string', 'in:lelang,hibah,musnah,daur_ulang'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'disposal_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        $baNumber = 'BA-ASET/'.now()->format('Ym').'/HAPUS-'.rand(100, 999);

        $notes = "Penghapusan Aset Yayasan ({$validated['disposal_method']}) No. BA: {$baNumber}. Alasan: {$validated['disposal_reason']}";
        if ($asset->notes) {
            $notes = $asset->notes."\n".$notes;
        }

        $asset->update([
            'status' => AssetStatus::Disposed,
            'salvage_value' => $validated['salvage_value'] ?? 0,
            'notes' => $notes,
        ]);

        return back()->with('success', "Penghapusan aset {$asset->name} ({$asset->asset_code}) berhasil diproses dengan nomor {$baNumber}.");
    }

    /**
     * Official printable Berita Acara Penghapusan Aset.
     */
    public function print(Request $request, Asset $asset): Response
    {
        abort_unless($request->user()->canManageDisposals(), 403, 'Anda tidak berwenang mencetak berita acara penghapusan aset.');

        $asset->load(['category', 'room.building.campus']);

        return Inertia::render('Disposals/Print', [
            'asset' => [
                'id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'category' => $asset->category?->name,
                'location' => ($asset->room?->building?->name ?? '-').' / '.($asset->room?->name ?? '-'),
                'purchase_date' => $asset->purchase_date?->format('d/m/Y'),
                'purchase_cost' => $asset->purchase_cost,
                'salvage_value' => $asset->salvage_value,
                'condition' => $asset->condition->label(),
                'status' => $asset->status->label(),
                'notes' => $asset->notes,
            ],
            'meta' => [
                'date' => now()->translatedFormat('d F Y'),
                'ba_code' => 'BA-ASET/'.now()->format('Ym').'/HAPUS-'.$asset->id,
            ],
        ]);
    }
}
