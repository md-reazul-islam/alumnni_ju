<?php

namespace Tests\Feature;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketplaceListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_alumnus_can_submit_a_listing_for_review(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = MarketplaceCategory::factory()->create();

        $response = $this->actingAs($user)->post(route('marketplace.store'), [
            'marketplace_category_id' => $category->id,
            'title' => 'Cozy 2BR Apartment',
            'description' => 'A lovely apartment near campus.',
            'price' => 1200,
            'price_unit' => 'per_month',
            'address' => '123 Main St',
            'city' => 'Springfield',
            'images' => [UploadedFile::fake()->image('photo.jpg')],
        ]);

        $response->assertRedirect(route('marketplace.mine'));
        $this->assertDatabaseHas('marketplace_listings', [
            'title' => 'Cozy 2BR Apartment',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_pending_listings_are_not_publicly_visible(): void
    {
        $listing = MarketplaceListing::factory()->create();

        $response = $this->get(route('marketplace.show', $listing));

        $response->assertNotFound();
    }

    public function test_admin_can_approve_a_pending_listing(): void
    {
        $admin = User::factory()->admin()->create();
        $listing = MarketplaceListing::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.marketplace.listings.approve', $listing));

        $response->assertRedirect();
        $this->assertSame('approved', $listing->fresh()->status);
    }

    public function test_admin_rejecting_a_listing_requires_a_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $listing = MarketplaceListing::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.marketplace.listings.reject', $listing), []);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertSame('pending', $listing->fresh()->status);
    }

    public function test_approved_listings_appear_in_public_marketplace(): void
    {
        $listing = MarketplaceListing::factory()->approved()->create(['title' => 'Sunny Studio for Rent']);

        $response = $this->get(route('marketplace.index'));

        $response->assertOk();
        $response->assertSee('Sunny Studio for Rent');
    }

    public function test_non_admin_cannot_approve_listings(): void
    {
        $user = User::factory()->create();
        $listing = MarketplaceListing::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.marketplace.listings.approve', $listing));

        $response->assertForbidden();
    }

    public function test_editing_an_approved_listing_resets_it_to_pending(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = MarketplaceCategory::factory()->create();
        $listing = MarketplaceListing::factory()->approved()->create([
            'user_id' => $user->id,
            'marketplace_category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->put(route('marketplace.update', $listing), [
            'marketplace_category_id' => $category->id,
            'title' => $listing->title,
            'description' => $listing->description,
            'price' => $listing->price,
            'price_unit' => $listing->price_unit,
            'address' => $listing->address,
        ]);

        $response->assertRedirect(route('marketplace.mine'));
        $this->assertSame('pending', $listing->fresh()->status);
        $this->assertNull($listing->fresh()->approved_at);
    }
}
