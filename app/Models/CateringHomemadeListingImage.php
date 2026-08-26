<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringHomemadeListingImage extends Model
{
    use HasFactory;

    protected $fillable = ['catering_homemade_listing_id', 'path', 'sort_order'];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(CateringHomemadeListing::class, 'catering_homemade_listing_id');
    }
}
