<?php

namespace Tests\Feature;

use App\Models\AlumniProfile;
use App\Models\AlumniStory;
use App\Models\Book;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\MarketplaceListing;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_requires_at_least_two_characters(): void
    {
        $response = $this->getJson('/search?q=a');

        $response->assertOk();
        $response->assertJson(['groups' => []]);
    }

    public function test_search_finds_a_public_alumnus_by_name(): void
    {
        $profile = AlumniProfile::factory()->verified()->public()->create();
        $profile->user->update(['first_name' => 'Zendaya', 'last_name' => 'Testperson']);

        $response = $this->getJson('/search?q=Zendaya');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Zendaya Testperson']);
    }

    public function test_search_excludes_alumni_only_profiles_from_public_search(): void
    {
        $profile = AlumniProfile::factory()->verified()->create(['profile_visibility' => AlumniProfile::VISIBILITY_ALUMNI]);
        $profile->user->update(['first_name' => 'Hiddenperson', 'last_name' => 'Notpublic']);

        $response = $this->getJson('/search?q=Hiddenperson');

        $response->assertOk();
        $response->assertJsonMissing(['title' => 'Hiddenperson Notpublic']);
    }

    public function test_search_finds_a_published_event(): void
    {
        Event::factory()->create(['title' => 'Spring Networking Mixer', 'status' => Event::STATUS_PUBLISHED]);

        $response = $this->getJson('/search?q=Networking');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Spring Networking Mixer']);
    }

    public function test_search_finds_an_approved_job(): void
    {
        JobPosting::factory()->approved()->create(['title' => 'Senior Backend Engineer']);

        $response = $this->getJson('/search?q=Backend');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Senior Backend Engineer']);
    }

    public function test_search_finds_published_news_and_stories(): void
    {
        $author = User::factory()->create();
        News::create([
            'author_id' => $author->id,
            'title' => 'University Launches New Scholarship Fund',
            'slug' => 'university-launches-new-scholarship-fund',
            'body' => 'Details about the fund.',
            'status' => News::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $profile = AlumniProfile::factory()->verified()->create();
        AlumniStory::create([
            'alumni_profile_id' => $profile->id,
            'title' => 'From Campus to Career: My Scholarship Journey',
            'slug' => 'from-campus-to-career-scholarship-journey',
            'story' => 'A story about a scholarship.',
            'status' => AlumniStory::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->getJson('/search?q=Scholarship');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'University Launches New Scholarship Fund']);
        $response->assertJsonFragment(['title' => 'From Campus to Career: My Scholarship Journey']);
    }

    public function test_search_finds_an_approved_marketplace_listing(): void
    {
        MarketplaceListing::factory()->approved()->create(['title' => 'Cozy Lakeside Cabin for Rent']);

        $response = $this->getJson('/search?q=Lakeside');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Cozy Lakeside Cabin for Rent']);
    }

    public function test_search_finds_an_available_library_book(): void
    {
        $donor = User::factory()->create();
        Book::create([
            'donor_id' => $donor->id,
            'title' => 'Deep Learning Foundations',
            'author' => 'Some Author',
            'status' => Book::STATUS_APPROVED,
        ]);

        $response = $this->getJson('/search?q=Foundations');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Deep Learning Foundations']);
    }
}
