<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\RoomBookingStatus;
use App\Models\Consumable;
use App\Models\LoanTransaction;
use App\Models\MaintenanceTicket;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;

class SystemNotificationService
{
    /**
     * Get list of real-time notifications for the given user.
     *
     * @return array<int, array{id: string, title: string, message: string, url: string, type: string, time: string}>
     */
    public function getNotificationsFor(User $user): array
    {
        $notifications = [];

        // 1. Civitas Notifications (Mahasiswa, Dosen, Tendik)
        if ($user->isCivitas()) {
            // Approved loans ready for pickup
            $approvedLoans = LoanTransaction::where('borrower_id', $user->id)
                ->where('status', LoanStatus::Approved->value)
                ->latest()
                ->take(3)
                ->get();

            foreach ($approvedLoans as $loan) {
                $notifications[] = [
                    'id' => 'loan_app_'.$loan->id,
                    'title' => 'Peminjaman Disetujui',
                    'message' => "Peminjaman {$loan->transaction_code} telah disetujui dan siap diambil di biro/lab.",
                    'url' => route('loans.show', $loan->id),
                    'type' => 'success',
                    'time' => $loan->updated_at?->diffForHumans() ?? 'Baru saja',
                ];
            }

            // Overdue or near-due loans
            $borrowedLoans = LoanTransaction::where('borrower_id', $user->id)
                ->where('status', LoanStatus::Borrowed->value)
                ->where('return_due_date', '<=', now()->addDay())
                ->latest()
                ->take(3)
                ->get();

            foreach ($borrowedLoans as $loan) {
                $isOverdue = $loan->return_due_date && $loan->return_due_date->isPast();
                $notifications[] = [
                    'id' => 'loan_due_'.$loan->id,
                    'title' => $isOverdue ? 'Jatuh Tempo Pengembalian' : 'Batas Pengembalian Besok',
                    'message' => $isOverdue
                        ? "Peminjaman {$loan->transaction_code} telah melewati batas pengembalian. Harap segera kembalikan."
                        : "Peminjaman {$loan->transaction_code} memiliki tenggat waktu pengembalian besok.",
                    'url' => route('loans.show', $loan->id),
                    'type' => $isOverdue ? 'danger' : 'warning',
                    'time' => $loan->return_due_date?->diffForHumans() ?? 'Segera',
                ];
            }

            // Approved room bookings
            $approvedBookings = RoomBooking::where('user_id', $user->id)
                ->where('status', RoomBookingStatus::Approved->value)
                ->where('booking_date', '>=', now()->toDateString())
                ->with('room')
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach ($approvedBookings as $booking) {
                $notifications[] = [
                    'id' => 'booking_app_'.$booking->id,
                    'title' => 'Reservasi Ruangan Disetujui',
                    'message' => "Reservasi {$booking->room?->name} ({$booking->booking_code}) telah disetujui pengelola.",
                    'url' => route('room-bookings.show', $booking->id),
                    'type' => 'success',
                    'time' => $booking->updated_at?->diffForHumans() ?? 'Baru saja',
                ];
            }

            // Maintenance ticket progress
            $updatedTickets = MaintenanceTicket::where('reporter_id', $user->id)
                ->whereIn('status', [MaintenanceStatus::InProgress->value, MaintenanceStatus::Completed->value])
                ->with('asset')
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach ($updatedTickets as $ticket) {
                $isCompleted = $ticket->status === MaintenanceStatus::Completed || $ticket->status?->value === 'completed';
                $notifications[] = [
                    'id' => 'ticket_'.$ticket->id,
                    'title' => $isCompleted ? 'Perbaikan Selesai' : 'Perbaikan Sedang Dikerjakan',
                    'message' => "Tiket {$ticket->ticket_code} ({$ticket->asset?->name}) ".($isCompleted ? 'telah tuntas diperbaiki.' : 'sedang ditangani oleh teknisi.'),
                    'url' => route('maintenance.show', $ticket->id),
                    'type' => $isCompleted ? 'success' : 'info',
                    'time' => $ticket->updated_at?->diffForHumans() ?? 'Baru saja',
                ];
            }
        }

        // 2. Teknisi Notifications
        if ($user->isTeknisi()) {
            $assignedTickets = MaintenanceTicket::where('technician_id', $user->id)
                ->whereNotIn('status', [MaintenanceStatus::Completed->value, MaintenanceStatus::Cancelled->value])
                ->with(['asset.room'])
                ->latest()
                ->take(5)
                ->get();

            foreach ($assignedTickets as $ticket) {
                $isUrgent = in_array($ticket->priority?->value ?? $ticket->priority, ['critical', 'high']);
                $notifications[] = [
                    'id' => 'tech_assigned_'.$ticket->id,
                    'title' => $isUrgent ? 'Tiket Darurat Ditugaskan' : 'Tugas Perbaikan Masuk',
                    'message' => "Tiket {$ticket->ticket_code}: {$ticket->asset?->name} (".($ticket->asset?->room?->name ?? 'Lokasi kampus').').',
                    'url' => route('maintenance.show', $ticket->id),
                    'type' => $isUrgent ? 'danger' : 'info',
                    'time' => $ticket->updated_at?->diffForHumans() ?? 'Baru saja',
                ];
            }

            // Urgent unassigned campus tickets
            $urgentTickets = MaintenanceTicket::whereIn('priority', ['critical', 'high'])
                ->whereNotIn('status', [MaintenanceStatus::Completed->value, MaintenanceStatus::Cancelled->value])
                ->whereNull('technician_id')
                ->with(['asset.room'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($urgentTickets as $ticket) {
                $notifications[] = [
                    'id' => 'tech_urgent_'.$ticket->id,
                    'title' => 'Kerusakan Kritis Butuh Penugasan',
                    'message' => "Tiket {$ticket->ticket_code} ({$ticket->asset?->name}) prioritas darurat dan belum ada teknisi.",
                    'url' => route('maintenance.show', $ticket->id),
                    'type' => 'danger',
                    'time' => $ticket->created_at?->diffForHumans() ?? 'Baru saja',
                ];
            }
        }

        // 3. PIC Ruangan / Laboran
        if ($user->isPicRuangan()) {
            $myRoomIds = Room::where('pic_user_id', $user->id)->pluck('id')->all();

            if (! empty($myRoomIds)) {
                $pendingBookings = RoomBooking::where('status', RoomBookingStatus::Pending->value)
                    ->whereIn('room_id', $myRoomIds)
                    ->with(['user', 'room'])
                    ->latest()
                    ->take(4)
                    ->get();

                foreach ($pendingBookings as $b) {
                    $notifications[] = [
                        'id' => 'pic_book_'.$b->id,
                        'title' => 'Permohonan Reservasi Ruang',
                        'message' => "{$b->user?->name} mengajukan reservasi {$b->room?->name}.",
                        'url' => route('room-bookings.show', $b->id),
                        'type' => 'warning',
                        'time' => $b->created_at?->diffForHumans() ?? 'Baru saja',
                    ];
                }

                $pendingLoans = LoanTransaction::where('status', LoanStatus::Pending->value)
                    ->whereHas('loanItems.asset', fn ($q) => $q->whereIn('room_id', $myRoomIds))
                    ->with(['borrower'])
                    ->latest()
                    ->take(4)
                    ->get();

                foreach ($pendingLoans as $l) {
                    $notifications[] = [
                        'id' => 'pic_loan_'.$l->id,
                        'title' => 'Permohonan Peminjaman Alat',
                        'message' => "{$l->borrower?->name} mengajukan peminjaman alat lab ({$l->transaction_code}).",
                        'url' => route('loans.show', $l->id),
                        'type' => 'warning',
                        'time' => $l->created_at?->diffForHumans() ?? 'Baru saja',
                    ];
                }
            }
        }

        // 4. Super Admin & Admin Fakultas
        if ($user->isSuperAdmin() || $user->isAdminFakultas()) {
            $adminPendingLoans = LoanTransaction::where('status', LoanStatus::Pending->value)
                ->with(['borrower'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($adminPendingLoans as $l) {
                $notifications[] = [
                    'id' => 'admin_loan_'.$l->id,
                    'title' => 'Verifikasi Peminjaman Alat',
                    'message' => "Peminjaman {$l->transaction_code} ({$l->borrower?->name}) menunggu persetujuan.",
                    'url' => route('loans.show', $l->id),
                    'type' => 'warning',
                    'time' => $l->created_at?->diffForHumans() ?? 'Baru saja',
                ];
            }

            $adminPendingBookings = RoomBooking::where('status', RoomBookingStatus::Pending->value)
                ->with(['user', 'room'])
                ->latest()
                ->take(3)
                ->get();

            foreach ($adminPendingBookings as $b) {
                $notifications[] = [
                    'id' => 'admin_book_'.$b->id,
                    'title' => 'Persetujuan Reservasi Ruangan',
                    'message' => "Reservasi {$b->room?->name} ({$b->booking_code}) oleh {$b->user?->name} menunggu review.",
                    'url' => route('room-bookings.show', $b->id),
                    'type' => 'warning',
                    'time' => $b->created_at?->diffForHumans() ?? 'Baru saja',
                ];
            }

            $lowStockItems = Consumable::whereColumn('current_stock', '<=', 'minimum_stock')
                ->take(2)
                ->get();

            foreach ($lowStockItems as $item) {
                $notifications[] = [
                    'id' => 'stock_'.$item->id,
                    'title' => 'Stok Logistik Kritis',
                    'message' => "Barang {$item->name} tersisa {$item->current_stock} {$item->unit} (min. {$item->minimum_stock}).",
                    'url' => route('consumables.index'),
                    'type' => 'danger',
                    'time' => 'Perhatian Stok',
                ];
            }
        }

        return array_slice($notifications, 0, 8);
    }
}
