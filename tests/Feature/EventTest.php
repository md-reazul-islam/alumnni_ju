<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_published_event(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.events.store'), [
            'title' => 'Annual Homecoming',
            'category' => 'reunion',
            'mode' => 'offline',
            'venue' => 'Main Hall',
            'event_date' => now()->addMonth()->format('Y-m-d'),
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['title' => 'Annual Homecoming', 'status' => 'published']);
    }

    public function test_non_admin_cannot_create_an_event(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.events.create'));

        $response->assertForbidden();
    }

    public function test_alumnus_can_register_for_an_open_event(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['event_date' => now()->addWeek()]);

        $response = $this->actingAs($user)->post(route('events.register', $event));

        $response->assertRedirect();
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);
    }

    public function test_alumnus_can_cancel_their_registration(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['event_date' => now()->addWeek()]);
        $event->registrations()->create(['user_id' => $user->id, 'status' => 'registered', 'registered_at' => now()]);

        $response = $this->actingAs($user)->delete(route('events.cancel', $event));

        $response->assertRedirect();
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_guest_cannot_register_for_an_event(): void
    {
        $event = Event::factory()->create();

        $response = $this->post(route('events.register', $event));

        $response->assertRedirect(route('login'));
    }
}
