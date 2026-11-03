<?php

namespace App\Enums;

enum ProcurementStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Ordered = 'ordered';
    case Received = 'received';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf Usulan',
            self::Submitted => 'Diajukan ke Sarpras',
            self::UnderReview => 'Dalam Telaah Teknis',
            self::Approved => 'Disetujui (Alokasi Dana)',
            self::Rejected => 'Ditolak',
            self::Ordered => 'Proses Pemesanan (PO)',
            self::Received => 'Barang Diterima Fisik',
            self::Completed => 'Selesai (Tercatat di Aset)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-slate-100 text-slate-700 border-slate-200',
            self::Submitted => 'bg-blue-50 text-blue-700 border-blue-200',
            self::UnderReview => 'bg-amber-50 text-amber-800 border-amber-200 font-semibold',
            self::Approved => 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold',
            self::Rejected => 'bg-rose-50 text-rose-700 border-rose-200',
            self::Ordered => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::Received => 'bg-cyan-50 text-cyan-800 border-cyan-200 font-bold',
            self::Completed => 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold',
        };
    }
}
