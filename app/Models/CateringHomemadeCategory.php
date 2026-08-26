<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringHomemadeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'commission_percentage', 'is_active'];

    protected function casts(): array
    {
        return [
            'commission_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(CateringHomemadeListing::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
