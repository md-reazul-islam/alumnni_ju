<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CateringProgramCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function foodItems(): BelongsToMany
    {
        return $this->belongsToMany(CateringFoodItem::class, 'catering_food_item_category');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(CateringOrder::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
