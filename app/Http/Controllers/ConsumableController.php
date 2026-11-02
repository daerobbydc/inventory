<?php

namespace App\Http\Controllers;

use App\Enums\ConsumableTransactionType;
use App\Models\Category;
use App\Models\Consumable;
use App\Models\ConsumableTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsumableController extends Controller
{
    /**
     * Display a listing of consumable items and stock alerts.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $stockStatus = $request->query('stock_status');

        $query = Consumable::with('category')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'low') {
            $query->whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>', 0);
        } elseif ($stockStatus === 'empty') {
            $query->where('current_stock', '<=', 0);
        } elseif ($stockStatus === 'safe') {
            $query->whereColumn('current_stock', '>', 'minimum_stock');
        }

        $consumables = $query->paginate(12)->withQueryString();

        // Summary metrics
        $all = Consumable::all();
        $metrics = [
            'total_items' => $all->count(),
            'total_valuation' => $all->sum(fn ($c) => $c->current_stock * $c->unit_price),
            'low_stock_count' => $all->filter(fn ($c) => $c->current_stock <= $c->minimum_stock && $c->current_stock > 0)->count(),
            'empty_stock_count' => $all->filter(fn ($c) => $c->current_stock <= 0)->count(),
        ];

        // Recent transactions
        $recentTransactions = ConsumableTransaction::with(['consumable', 'user'])
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::orderBy('name')->get(['id', 'name', 'code']);

        return Inertia::render('Consumables/Index', [
            'consumables' => $consumables,
            'metrics' => $metrics,
            'recentTransactions' => $recentTransactions,
            'categories' => $categories,
            'filters' => [
                'search' => $search ?? '',
                'category_id' => $categoryId ?? 'all',
                'stock_status' => $stockStatus ?? 'all',
            ],
        ]);
    }

    /**
     * Store a newly created consumable item.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->canManageConsumables(), 403, 'Anda tidak berwenang mengelola persediaan barang habis pakai.');

        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:50', 'unique:consumables,item_code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:30'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $consumable = Consumable::create($validated);

        if ($consumable->current_stock > 0) {
            ConsumableTransaction::create([
                'consumable_id' => $consumable->id,
                'user_id' => $request->user()->id,
                'type' => ConsumableTransactionType::In,
                'quantity' => $consumable->current_stock,
                'reference_number' => 'INIT/'.now()->format('Ymd').'/'.$consumable->item_code,
                'notes' => 'Pencatatan saldo awal persediaan barang habis pakai.',
            ]);
        }

        return back()->with('success', "Barang {$consumable->name} berhasil ditambahkan ke inventaris persediaan.");
    }

    /**
     * Update the specified consumable item.
     */
    public function update(Request $request, Consumable $consumable): RedirectResponse
    {
        abort_unless($request->user()->canManageConsumables(), 403, 'Anda tidak berwenang mengelola persediaan barang habis pakai.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $consumable->update($validated);

        return back()->with('success', "Data barang {$consumable->name} berhasil diperbarui.");
    }

    /**
     * Process stock transaction: stock-in (restock) or stock-out (requisition).
     */
    public function transaction(Request $request, Consumable $consumable): RedirectResponse
    {
        abort_unless($request->user()->canManageConsumables(), 403, 'Anda tidak berwenang mencatat transaksi persediaan barang habis pakai.');

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $isOut = $validated['type'] === 'out';

        if ($isOut && $consumable->current_stock < $validated['quantity']) {
            return back()->with('error', "Stok tidak mencukupi! Persediaan saat ini hanya tersisa {$consumable->current_stock} {$consumable->unit}.");
        }

        // Adjust stock
        if ($isOut) {
            $consumable->decrement('current_stock', $validated['quantity']);
        } else {
            $consumable->increment('current_stock', $validated['quantity']);
        }

        $refNo = $validated['reference_number'] ?: strtoupper($validated['type']).'/'.now()->format('Ym').'/'.rand(1000, 9999);

        ConsumableTransaction::create([
            'consumable_id' => $consumable->id,
            'user_id' => $request->user()->id,
            'type' => $validated['type'] === 'in' ? ConsumableTransactionType::In : ConsumableTransactionType::Out,
            'quantity' => $validated['quantity'],
            'reference_number' => $refNo,
            'notes' => $validated['notes'],
        ]);

        $actionLabel = $isOut ? 'Permintaan keluar' : 'Penambahan stok masuk';

        return back()->with('success', "{$actionLabel} sebanyak {$validated['quantity']} {$consumable->unit} untuk {$consumable->name} berhasil dicatat.");
    }
}
