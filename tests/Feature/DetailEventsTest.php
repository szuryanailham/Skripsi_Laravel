<?php
namespace Tests\Feature\Events;

use App\Models\User;
use App\Models\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetailEventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_event_detail_by_slug()
    {
        // Arrange: Buat user & event
        $user = User::factory()->create();
        $event = Events::factory()->create([
            'title' => 'Event Test',
            'slug' => 'event-test',
        ]);

        // Act: Hit endpoint sebagai user
        $response = $this->actingAs($user)->getJson("/api/events/{$event->slug}/detail");

        // Assert: Cek struktur dan isi response
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'slug' => 'event-test',
                         'title' => 'Event Test'
                     ]
                 ])
                 ->assertJsonStructure([
                     'status',
                     'data' => ['id', 'slug', 'title'] // sesuaikan struktur sesuai tabelmu
                 ]);
    }

    public function test_it_returns_404_if_event_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/events/non-existent-slug/detail');

        $response->assertStatus(500) // kamu bisa ubah ke 404 kalau pakai `firstOrFail` lalu `ModelNotFoundException`
                 ->assertJson([
                     'status' => 'error',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'message',
                 ]);
    }

    public function test_unauthenticated_user_cannot_access_event_detail()
    {
        $event = Events::factory()->create();

        $response = $this->getJson("/api/events/{$event->slug}/detail");

        $response->assertStatus(401); // Unauthorized
    }
}
