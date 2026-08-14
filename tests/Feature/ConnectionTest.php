<?php

namespace Tests\Feature;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumnus_can_send_a_connection_request(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $response = $this->actingAs($sender)
            ->postJson(route('connections.store', $recipient));

        $response->assertOk();
        $this->assertDatabaseHas('connections', [
            'requester_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_send_duplicate_connection_requests(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        Connection::create(['requester_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'pending']);

        $response = $this->actingAs($sender)->postJson(route('connections.store', $recipient));

        $response->assertStatus(422);
    }

    public function test_recipient_can_accept_a_connection_request(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $connection = Connection::create(['requester_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'pending']);

        $response = $this->actingAs($recipient)->postJson(route('connections.accept', $connection));

        $response->assertOk();
        $this->assertSame('accepted', $connection->fresh()->status);
    }

    public function test_only_the_recipient_can_accept_a_connection_request(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();
        $intruder = User::factory()->create();
        $connection = Connection::create(['requester_id' => $sender->id, 'recipient_id' => $recipient->id, 'status' => 'pending']);

        $response = $this->actingAs($intruder)->postJson(route('connections.accept', $connection));

        $response->assertForbidden();
        $this->assertSame('pending', $connection->fresh()->status);
    }
}
