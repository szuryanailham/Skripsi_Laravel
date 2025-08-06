<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Events;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_order_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $event = Events::factory()->create();

        $payload = [
            'event_id' => $event->id,
            'total_amount' => 250000,
        ];

        // Act
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/orders', $payload);

        // Assert
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Order created successfully',
                     'order' => [
                         'event_id' => $event->id,
                         'total_amount' => 250000,
                         'status' => 'pending',
                     ]
                 ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_amount' => 250000,
            'status' => 'pending',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_validation_error_when_data_invalid()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
            'event_id' => null,
            'total_amount' => 'invalid'
        ]);

        // Assert
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['event_id', 'total_amount']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_unauthorized_when_user_not_authenticated()
    {
        // Act
        $response = $this->postJson('/api/orders', [
            'event_id' => 1,
            'total_amount' => 10000,
        ]);

        // Assert
        $response->assertStatus(401); // Unauthorized
    }

    
    #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_see_his_order_detail()
    {
        // Arrange
        $user = User::factory()->create(); 
        $event = Events::factory()->create();
        $this->actingAs($user); 

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        // Act
        $response = $this->getJson("/api/orders/{$order->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $order->id,
        ]);
    }

     #[\PHPUnit\Framework\Attributes\Test]
    public function unauthenticated_user_cannot_access_order()
    {
        // Arrange
        $user = User::factory()->create();
        $event = Events::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        // Act
        $response = $this->getJson("/api/orders/{$order->id}");

        // Assert
        $response->assertStatus(401); // unauthorized
    }

    
     #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_404_if_order_not_found()
{
    // Buat user dan login
    $user = User::factory()->create();
    $this->actingAs($user);

    // Pastikan tidak ada order dengan id 9999
    $response = $this->getJson('/api/orders/9999');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Order not found or you do not have access to this order.'
             ]);
}

 #[\PHPUnit\Framework\Attributes\Test]
public function it_returns_404_if_order_belongs_to_another_user()
{
    // Buat 2 user
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Buat event karena Order butuh event_id
    $event = Events::factory()->create();

    // Buat order milik user lain
    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
        'event_id' => $event->id,
    ]);

    // Login sebagai user yang tidak memiliki order
    Sanctum::actingAs($user);

    // Akses order milik orang lain
    $response = $this->getJson("/api/orders/{$order->id}");

    // Pastikan mendapat 404
    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Order not found or you do not have access to this order.',
             ]);
}



}

