<?php

namespace App\Http\Controllers;

use App\Enums\StockOpnameItemStatus;
use App\Enums\StockOpnameStatus;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\Room;
use App\Models\StockOpnameItem;
use App\Models\StockOpnameSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockOpnameController extends Controller
{
    /**
     * Show live QR camera scanner and audit workbench.
     */
    public function scanner(Request $request): Response
    {
        // Get or create active session
        $session = StockOpnameSession::where('status', StockOpnameStatus::Ongoing)->first();

        if (! $session) {
            $session = StockOpnameSession::create([
                'session_code' => 'SO/'.now()->format('Y').'/SM'.(now()->month > 6 ? '2' : '1').'/001',
                'name' => 'Sensus & Audit Fisik Aset Yayasan TA '.now()->format('Y'),
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'status' => StockOpnameStatus::Ongoing,
                'created_by_id' => $request->user()->id,
                'notes' => 'Sesi sensus dan verifikasi fisik inventaris Universitas Al-Khairiyah.',
            ]);
        }

        $rooms = Room::with('building.campus')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'building_id']);

        $recentItems = StockOpnameItem::with(['asset.category', 'expectedRoom.building', 'scannedRoom.building', 'scannedBy'])
            ->where('stock_opname_session_id', $session->id)
            ->latest()
            ->take(15)
            ->get();

        $stats = [
            'total_verified' => StockOpnameItem::where('stock_opname_session_id', $session->id)->count(),
            'match_count' => StockOpnameItem::where('stock_opname_session_id', $session->id)->where('status', StockOpnameItemStatus::Match)->count(),
            'misplaced_count' => StockOpnameItem::where('stock_opname_session_id', $session->id)->where('status', StockOpnameItemStatus::Misplaced)->count(),
            'total_assets' => Asset::count(),
        ];

        return Inertia::render('StockOpname/Scanner', [
            'session' => $session,
            'rooms' => $rooms,
            'recentItems' => $recentItems,
            'stats' => $stats,
        ]);
    }

    /**
     * Look up asset by scanned QR code token or asset code.
     */
    public function lookup(Request $request): JsonResponse
    {
        $code = trim($request->query('code', ''));

        if (! $code) {
            return response()->json(['error' => 'Kode QR kosong'], 422);
        }

        $asset = Asset::with(['category', 'room.building.campus'])
            ->where('qr_code_token', $code)
            ->orWhere('asset_code', $code)
            ->first();

        if (! $asset) {
            return response()->json(['error' => "Aset dengan kode '{$code}' tidak ditemukan di sistem inventaris"], 404);
        }

        $currentRoomId = $request->query('room_id');
        $isMatch = $currentRoomId ? ((int) $asset->room_id === (int) $currentRoomId) : true;

        return response()->json([
            'asset' => [
                'id' => $asset->id,
                'asset_code' => $asset->asset_code,
                'name' => $asset->name,
                'category' => $asset->category?->name,
                'condition' => $asset->condition->label(),
                'status' => $asset->status->label(),
                'room_id' => $asset->room_id,
                'room_name' => $asset->room?->name,
                'building_name' => $asset->room?->building?->name,
            ],
            'is_match' => $isMatch,
            'verdict' => $isMatch ? 'match' : 'misplaced',
        ]);
    }

    /**
     * Record verification result into stock opname session.
     */
    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stock_opname_session_id' => ['required', 'exists:stock_opname_sessions,id'],
            'asset_id' => ['required', 'exists:assets,id'],
            'scanned_room_id' => ['required', 'exists:rooms,id'],
            'condition' => ['required', 'string'],
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'relocate_to_here' => ['nullable', 'boolean'],
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);
        $expectedRoomId = $asset->room_id;
        $isMisplaced = (int) $expectedRoomId !== (int) $validated['scanned_room_id'];

        $itemStatus = $isMisplaced ? StockOpnameItemStatus::Misplaced : StockOpnameItemStatus::Match;

        // Save or update audit item
        StockOpnameItem::updateOrCreate(
            [
                'stock_opname_session_id' => $validated['stock_opname_session_id'],
                'asset_id' => $asset->id,
            ],
            [
                'expected_room_id' => $expectedRoomId,
                'scanned_room_id' => $validated['scanned_room_id'],
                'scanned_by_id' => $request->user()->id,
                'scanned_at' => now(),
                'status' => $itemStatus,
                'condition' => $validated['condition'],
                'notes' => $validated['notes'],
            ]
        );

        // Update physical asset condition
        $asset->condition = $validated['condition'];

        // If relocated, record asset movement and update room_id
        if (! empty($validated['relocate_to_here']) && $isMisplaced) {
            AssetMovement::create([
                'asset_id' => $asset->id,
                'from_room_id' => $expectedRoomId,
                'to_room_id' => $validated['scanned_room_id'],
                'moved_by_id' => $request->user()->id,
                'moved_at' => now(),
                'reason' => 'Relokasi otomatis dari hasil verifikasi audit fisik sensus aset yayasan.',
            ]);

            $asset->room_id = $validated['scanned_room_id'];
        }

        $asset->save();

        return back()->with('success', "Verifikasi aset {$asset->name} ({$asset->asset_code}) berhasil disimpan.");
    }
}
