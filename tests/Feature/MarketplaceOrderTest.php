<?php

namespace Tests\Feature;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_express_interest_and_a_conversation_is_created(): void
    {
        User::factory()->admin()->create();

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->approved()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($buyer)->post(route('marketplace.inquire', $listing));

        $order = MarketplaceOrder::where('marketplace_listing_id', $listing->id)->where('buyer_id', $buyer->id)->first();

        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);
        $this->assertNotNull($order->buyerConversation);
        $this->assertTrue($order->buyerConversation->participants->contains($buyer));
        $response->assertRedirect(route('messages.index', $order->buyerConversation));
    }

    public function test_seller_cannot_inquire_about_their_own_listing(): void
    {
        $seller = User::factory()->create();
        $listing = MarketplaceListing::factory()->approved()->create(['user_id' => $seller->id]);

        $response = $this->actingAs($seller)->post(route('marketplace.inquire', $listing));

        $response->assertStatus(422);
    }

    public function test_repeat_inquiry_reuses_the_existing_pending_order(): void
    {
        User::factory()->admin()->create();

        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->approved()->create();

        $this->actingAs($buyer)->post(route('marketplace.inquire', $listing));
        $this->actingAs($buyer)->post(route('marketplace.inquire', $listing));

        $this->assertSame(1, MarketplaceOrder::where('marketplace_listing_id', $listing->id)->where('buyer_id', $buyer->id)->count());
    }

    public function test_admin_can_complete_an_order_and_commission_is_calculated(): void
    {
        $admin = User::factory()->admin()->create();
        $category = MarketplaceCategory::factory()->create(['commission_percentage' => 10]);
        $listing = MarketplaceListing::factory()->approved()->create(['marketplace_category_id' => $category->id]);
        $order = MarketplaceOrder::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'seller_id' => $listing->user_id,
            'status' => 'ongoing',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.marketplace.orders.status', $order), [
            'status' => 'completed',
            'final_price' => 1000,
        ]);

        $response->assertRedirect();
        $order->refresh();
        $this->assertSame('completed', $order->status);
        $this->assertEquals(1000, $order->final_price);
        $this->assertEquals(10, $order->commission_percentage_snapshot);
        $this->assertEquals(100, $order->commission_amount);
    }

    public function test_non_marketplace_admin_cannot_view_orders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.marketplace.orders.index'));

        $response->assertForbidden();
    }
}
