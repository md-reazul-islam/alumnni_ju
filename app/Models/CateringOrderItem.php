<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CateringOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'catering_order_id', 'catering_food_item_id', 'custom_item_name',
        'quantity', 'unit_price', 'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(CateringOrder::class, 'catering_order_id');
    }

    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(CateringFoodItem::class, 'catering_food_item_id');
    }

    public function isCustom(): bool
    {
        return is_null($this->catering_food_item_id);
    }

    public function isPriced(): bool
    {
        return ! is_null($this->unit_price);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->catering_food_item_id ? $this->foodItem->name : $this->custom_item_name;
    }
}
