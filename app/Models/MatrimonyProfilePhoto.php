<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyProfilePhoto extends Model
{
    use HasFactory;

    protected $fillable = ['matrimony_profile_id', 'path', 'is_primary', 'sort_order'];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MatrimonyProfile::class, 'matrimony_profile_id');
    }
}
