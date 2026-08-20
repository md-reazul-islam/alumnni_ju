<?php

namespace Database\Factories;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceOrder>
 */
class MarketplaceOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'marketplace_listing_id' => MarketplaceListing::factory(),
            'buyer_id' => User::factory(),
            'seller_id' => User::factory(),
            'status' => MarketplaceOrder::STATUS_PENDING,
        ];
    }
}
