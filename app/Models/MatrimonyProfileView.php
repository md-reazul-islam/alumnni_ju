<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatrimonyProfileView extends Model
{
    use HasFactory;

    protected $fillable = ['matrimony_profile_id', 'viewer_id'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MatrimonyProfile::class, 'matrimony_profile_id');
    }

    public function viewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}
