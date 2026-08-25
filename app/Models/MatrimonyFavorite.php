<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyFavorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'matrimony_profile_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MatrimonyProfile::class, 'matrimony_profile_id');
    }
}
