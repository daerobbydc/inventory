<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nip_nim',
        'phone',
        'role',
        'department',
        'faculty_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Rooms managed by this user as PIC.
     */
    public function managedRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'pic_user_id');
    }

    /**
     * Loan transactions submitted by this user.
     */
    public function loanTransactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class, 'borrower_id');
    }

    /**
     * Maintenance tickets reported by this user.
     */
    public function reportedTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'reporter_id');
    }

    /**
     * Maintenance tickets assigned to this user as technician.
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'technician_id');
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * Check if user is Admin Fakultas.
     */
    public function isAdminFakultas(): bool
    {
        return $this->role === UserRole::AdminFakultas;
    }

    /**
     * Check if user is PIC Ruangan / Laboran.
     */
    public function isPicRuangan(): bool
    {
        return $this->role === UserRole::PicRuangan;
    }

    /**
     * Check if user is Teknisi.
     */
    public function isTeknisi(): bool
    {
        return $this->role === UserRole::Teknisi;
    }

    /**
     * Check if user is Dosen.
     */
    public function isDosen(): bool
    {
        return $this->role === UserRole::Dosen;
    }

    /**
     * Check if user is Mahasiswa.
     */
    public function isMahasiswa(): bool
    {
        return $this->role === UserRole::Mahasiswa;
    }

    /**
     * Check if user is Tenaga Kependidikan (Tendik).
     */
    public function isTendik(): bool
    {
        return $this->role === UserRole::Tendik;
    }

    /**
     * Check if user is Civitas (Mahasiswa, Dosen, Tendik).
     */
    public function isCivitas(): bool
    {
        return in_array($this->role, [
            UserRole::Mahasiswa,
            UserRole::Dosen,
            UserRole::Tendik,
        ], true);
    }

    /**
     * Check if user is Staff (Super Admin, Admin Fakultas, PIC Ruangan).
     */
    public function isStaff(): bool
    {
        return in_array($this->role, [
            UserRole::SuperAdmin,
            UserRole::AdminFakultas,
            UserRole::PicRuangan,
        ], true);
    }

    /**
     * Determine if user can manage campus assets.
     */
    public function canManageAssets(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can manage/approve loan requests.
     */
    public function canManageLoans(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can manage consumables/ATK inventory.
     */
    public function canManageConsumables(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can manage asset disposals.
     */
    public function canManageDisposals(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can manage system settings and users.
     */
    public function canManageSettings(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Determine if user can perform audit / stock opname scanner.
     */
    public function canPerformAudit(): bool
    {
        return $this->isStaff() || $this->isTeknisi();
    }

    /**
     * Faculty that this user belongs to.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Room bookings submitted by this user.
     */
    public function roomBookings(): HasMany
    {
        return $this->hasMany(RoomBooking::class);
    }

    /**
     * Determine if user can manage/approve room reservations.
     */
    public function canManageRoomBookings(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can update maintenance ticket progress & dispatch.
     */
    public function canUpdateMaintenance(): bool
    {
        return $this->isStaff() || $this->isTeknisi();
    }

    /**
     * Determine if user can create asset procurement proposals.
     */
    public function canCreateProcurement(): bool
    {
        return $this->isStaff();
    }

    /**
     * Determine if user can approve/review procurement proposals.
     */
    public function canManageProcurements(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Procurement requests submitted by this user.
     */
    public function procurementRequests(): HasMany
    {
        return $this->hasMany(ProcurementRequest::class);
    }
}
