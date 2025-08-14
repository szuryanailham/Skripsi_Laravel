<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;


class ManageEventByAdminTest  extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_event_index()
{
    // Membuat user admin
    $admin = User::factory()->create([
    'role' => 'admin',
]);

    Sanctum::actingAs($admin, ['*']);

    Events::factory()->count(3)->create();

    $response = $this->getJson('/api/admin/events');

   $response->assertJsonStructure([
    'status',
    'data' => [
        '*' => [
            'id',
            'title',
            'slug',
            'description',
            'price',
            'location',
            'date',
            'time',
            'created_at',
            'updated_at',
        ],
    ],
]);

}

    public function test_non_admin_cannot_access_event_index()
    {
        $user = User::factory()->create([
            'role' => "user",
        ]);

        $response = $this->actingAs($user)->getJson('/api/admin/events');

        $response->assertStatus(403); 
    }

    public function test_unauthenticated_user_cannot_access_event_index()
    {
        $response = $this->getJson('/api/admin/events');

        $response->assertStatus(401); // Tidak login
    }


      public function test_admin_can_create_event()
    {
        // Buat user admin
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Login menggunakan Sanctum
        Sanctum::actingAs($admin, ['*']);

        // Payload valid
        $payload = [
            'title' => 'Konser Musik Jogja',
            'slug' => 'konser-musik-jogja',
            'description' => 'Konser musik tahunan di Yogyakarta.',
            'price' => 150000,
            'location' => 'Stadion Mandala Krida',
            'date' => '2025-10-10',
            'time' => '19:00:00',
        ];

        // Request ke endpoint
        $response = $this->postJson('/api/admin/events', $payload);

        // Assertion
        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'title' => 'Konser Musik Jogja',
                         'slug' => 'konser-musik-jogja',
                         'description' => 'Konser musik tahunan di Yogyakarta.',
                         'price' => 150000,
                         'location' => 'Stadion Mandala Krida',
                         'date' => '2025-10-10',
                         'time' => '19:00:00',
                     ],
                 ]);

        // Pastikan data tersimpan di database
        $this->assertDatabaseHas('events', [
            'title' => 'Konser Musik Jogja',
        ]);
    }

     public function test_guest_cannot_create_event()
    {
        $payload = [
            'title' => 'Konser Tanpa Login',
            'slug' => 'konser-tanpa-login',
            'description' => 'Harusnya gagal.',
            'price' => 100000,
            'location' => 'Tempat Rahasia',
            'date' => '2025-11-01',
            'time' => '20:00:00',
        ];

        $response = $this->postJson('/api/admin/events', $payload);

        $response->assertStatus(401); // Unauthorized
    }


public function test_admin_can_update_event()
{

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin, ['*']);

    $event = Events::factory()->create([
        'title' => 'Innovative Tech Summit 2025',
        'slug' => 'innovative-tech-summit-2025',
        'description' => 'Acara teknologi tahunan.',
        'price' => 150000,
        'location' => 'Stadion Mandala Krida',
        'date' => '2025-10-10',
        'time' => '19:00:00',
    ]);


    $payload = [
        'title' => 'Updating Innovative Tech Summit 2025 Part 1',
    ];

    $response = $this->putJson("/api/admin/events/{$event->id}", $payload);

  $response->assertStatus(200)
         ->assertJson([
             'message' => 'Event updated successfully',
             'event' => [
                 'id' => $event->id,
                 'title' => 'Updating Innovative Tech Summit 2025 Part 1',
             ],
         ]);


    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Updating Innovative Tech Summit 2025 Part 1',
    ]);
}


public function test_admin_cannot_update_event_with_invalid_data()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin, ['*']);

    $event = Events::factory()->create();

    // Kirim data kosong
    $payload = [
        'title' => '',
    ];

    $response = $this->putJson("/api/admin/events/{$event->id}", $payload);

    $response->assertStatus(422) // Unprocessable Entity
             ->assertJsonValidationErrors(['title']);
}

public function test_non_admin_cannot_update_event()
{
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    Sanctum::actingAs($user, ['*']);

    $event = Events::factory()->create();

    $payload = [
        'title' => 'Update oleh User Biasa',
    ];

    $response = $this->putJson("/api/admin/events/{$event->id}", $payload);

    $response->assertStatus(403); // Forbidden
}

public function test_admin_can_delete_event()
{
    // Buat admin
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin, ['*']);

    // Buat event
    $event = Events::factory()->create();

    // Request delete
    $response = $this->deleteJson("/api/admin/events/{$event->id}");

    // Assertion
    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Event deleted successfully',
             ]);

    // Pastikan event sudah hilang dari database
    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
    ]);
}

public function test_non_admin_cannot_delete_event()
{
    $user = User::factory()->create([
        'role' => 'user',
    ]);

    Sanctum::actingAs($user, ['*']);

    $event = Events::factory()->create();

    $response = $this->deleteJson("/api/admin/events/{$event->id}");

    $response->assertStatus(403); // Forbidden
}


public function test_admin_cannot_delete_non_existing_event()
{
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin, ['*']);

    $response = $this->deleteJson("/api/admin/events/999999");

    $response->assertStatus(404);
}






}
