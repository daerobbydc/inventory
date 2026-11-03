<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Faculty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'dean_name',
        'phone',
        'email',
        'description',
    ];

    /**
     * Rooms belonging to this faculty.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Users (lecturers, students, staff) belonging to this faculty.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Assets located in rooms belonging to this faculty.
     */
    public function assets(): HasManyThrough
    {
        return $this->hasManyThrough(Asset::class, Room::class);
    }

    /**
     * Procurement requests initiated by this faculty.
     */
    public function procurementRequests(): HasMany
    {
        return $this->hasMany(ProcurementRequest::class);
    }
}
