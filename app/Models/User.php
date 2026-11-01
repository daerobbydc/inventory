<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
