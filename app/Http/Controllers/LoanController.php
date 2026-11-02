<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    /**
     * Display a listing of loan transactions.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $status = $request->input('status');
        $search = $request->input('search');

        $user = $request->user();
        $isStaff = $user->isStaff();

        $loans = LoanTransaction::query()
            ->with(['borrower', 'approver', 'loanItems.asset'])
            ->when(! $isStaff, function ($q) use ($user) {
                // Mahasiswa & Dosen normally view their own loan history
                $q->where('borrower_id', $user->id);
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('transaction_code', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhereHas('borrower', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Loans/Index', [
            'loans' => $loans,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
            'isStaff' => $isStaff,
            'statuses' => collect(LoanStatus::cases())->map(fn ($s) => [
                'value' => $s->value,
                'label' => $s->label(),
            ]),
        ]);
    }

    /**
     * Show the form for creating a new loan request.
     */
    public function create(): Response
    {
        $lastId = (LoanTransaction::max('id') ?? 0) + 1;
        $suggestedCode = 'LOAN/'.date('Ym').'/'.str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

        $availableAssets = Asset::where('status', AssetStatus::Available->value)
            ->with(['category', 'room.building'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Loans/Create', [
            'suggestedCode' => $suggestedCode,
            'availableAssets' => $availableAssets,
        ]);
    }

    /**
     * Store a newly created loan request in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loan_date' => ['required', 'date'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'purpose' => ['required', 'string', 'min:5'],
            'asset_ids' => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['required', 'exists:assets,id'],
        ]);

        $lastId = (LoanTransaction::max('id') ?? 0) + 1;
        $transactionCode = 'LOAN/'.date('Ym').'/'.str_pad((string) $lastId, 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $validated, $transactionCode) {
            $loan = LoanTransaction::create([
                'transaction_code' => $transactionCode,
                'borrower_id' => $request->user()->id,
                'loan_date' => $validated['loan_date'],
                'expected_return_date' => $validated['expected_return_date'],
                'status' => LoanStatus::Pending,
                'purpose' => $validated['purpose'],
            ]);

            foreach ($validated['asset_ids'] as $assetId) {
                $asset = Asset::findOrFail($assetId);

                LoanItem::create([
                    'loan_transaction_id' => $loan->id,
                    'asset_id' => $asset->id,
                    'initial_condition' => $asset->condition,
                    'notes' => 'Kondisi awal saat diajukan pinjam',
                ]);
            }
        });

        return redirect()->route('loans.index')
            ->with('success', 'Permintaan peminjaman berhasil diajukan dan sedang menunggu persetujuan.');
    }

    /**
     * Display the specified loan transaction voucher.
     */
    public function show(Request $request, LoanTransaction $loan): Response
    {
        $user = $request->user();
        $canManage = $user->canManageLoans();

        // Non-staff can only view their own loan requests
        if (! $canManage && $loan->borrower_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat transaksi peminjaman ini.');
        }

        $loan->load(['borrower', 'approver', 'loanItems.asset.room.building', 'loanItems.asset.category']);

        return Inertia::render('Loans/Show', [
            'loan' => $loan,
            'canManage' => $canManage,
            'conditions' => collect(AssetCondition::cases())->map(fn ($c) => [
                'value' => $c->value,
                'label' => $c->label(),
            ]),
        ]);
    }

    /**
     * Approve the loan request.
     */
    public function approve(Request $request, LoanTransaction $loan): RedirectResponse
    {
        abort_unless($request->user()->canManageLoans(), 403, 'Hanya petugas Sarpras/PIC yang dapat menyetujui peminjaman.');

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string'],
        ]);

        $loan->update([
            'status' => LoanStatus::Approved,
            'approver_id' => $request->user()->id,
            'approval_notes' => $validated['approval_notes'] ?? 'Disetujui oleh petugas Sarpras/Laboran.',
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Peminjaman telah disetujui. Barang siap diserahterimakan.');
    }

    /**
     * Reject the loan request.
     */
    public function reject(Request $request, LoanTransaction $loan): RedirectResponse
    {
        abort_unless($request->user()->canManageLoans(), 403, 'Hanya petugas Sarpras/PIC yang dapat menolak peminjaman.');

        $validated = $request->validate([
            'approval_notes' => ['required', 'string', 'min:3'],
        ]);

        $loan->update([
            'status' => LoanStatus::Rejected,
            'approver_id' => $request->user()->id,
            'approval_notes' => $validated['approval_notes'],
        ]);

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Peminjaman telah ditolak dengan alasan yang tercatat.');
    }

    /**
     * Confirm physical handover of the assets to the borrower.
     */
    public function handover(Request $request, LoanTransaction $loan): RedirectResponse
    {
        abort_unless($request->user()->canManageLoans(), 403, 'Hanya petugas Sarpras/PIC yang dapat memproses serah terima barang.');

        DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => LoanStatus::Borrowed,
            ]);

            // Update all related assets to borrowed
            foreach ($loan->loanItems as $item) {
                $item->asset->update([
                    'status' => AssetStatus::Borrowed,
                ]);
            }
        });

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Serah terima barang berhasil dikonfirmasi. Status aset kini tercatat sedang dipinjam.');
    }

    /**
     * Process the physical return of the assets.
     */
    public function returnItems(Request $request, LoanTransaction $loan): RedirectResponse
    {
        abort_unless($request->user()->canManageLoans(), 403, 'Hanya petugas Sarpras/PIC yang dapat memproses pengembalian barang.');

        $validated = $request->validate([
            'return_conditions' => ['required', 'array'],
            'return_conditions.*' => ['required', 'string'],
            'return_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $loan->update([
                'status' => LoanStatus::Returned,
                'actual_return_date' => now()->toDateString(),
            ]);

            foreach ($loan->loanItems as $item) {
                $conditionValue = $validated['return_conditions'][$item->id] ?? AssetCondition::Good->value;

                $item->update([
                    'return_condition' => $conditionValue,
                    'notes' => $validated['return_notes'] ?? 'Dikembalikan dalam kondisi diperiksa.',
                ]);

                // Update asset availability and condition
                $newStatus = ($conditionValue === AssetCondition::HeavyDamage->value)
                    ? AssetStatus::Maintenance
                    : AssetStatus::Available;

                $item->asset->update([
                    'status' => $newStatus,
                    'condition' => $conditionValue,
                ]);
            }
        });

        return redirect()->route('loans.show', $loan)
            ->with('success', 'Pengembalian barang berhasil diproses dan dicatat dalam sistem.');
    }
}
