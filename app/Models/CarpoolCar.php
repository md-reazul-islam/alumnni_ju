<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarpoolCar extends Model
{
    use HasFactory;

    protected $fillable = [
        'carpool_driver_profile_id', 'make', 'model', 'year', 'color', 'plate_number', 'total_seats', 'photo',
    ];

    public function driverProfile(): BelongsTo
    {
        return $this->belongsTo(CarpoolDriverProfile::class, 'carpool_driver_profile_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CarpoolSchedule::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->year} {$this->make} {$this->model}");
    }
}
