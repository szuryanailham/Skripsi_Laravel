<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Event;
use App\Models\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrintTicketTest extends TestCase
{
    use RefreshDatabase;


 #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_ticket_if_order_is_completed_and_owned_by_user()
    {
        $user = User::factory()->create();
        $event = Events::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'completed'
        ]);

        $this->actingAs($user);

        $response = $this->getJson("/api/orders/{$order->order_number}/ticket");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Here is your ticket!',
                'ticket' => [
                    'order_number' => $order->order_number,
                    'event' => [
                        'name' => $event->name,
                        'date' => $event->date,
                        'location' => $event->location,
                    ],
                    'user_name' => $user->name,
                    'total_amount' => $order->total_amount,
                    'status' => 'completed'
                ]
            ]);
    }

 
 #[\PHPUnit\Framework\Attributes\Test]
   public function it_returns_unauthorized_if_user_not_authenticated()
{
    // Buat user dan event terlebih dahulu
    $user = User::factory()->create();
    $event = Events::factory()->create();

    // Buat order dengan user dan event yang valid
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
    ]);

    // Akses endpoint tanpa autentikasi
    $response = $this->getJson("/api/orders/{$order->order_number}/ticket");

    // Pastikan response unauthorized
    $response->assertStatus(401);
}
 #[\PHPUnit\Framework\Attributes\Test]
   public function it_returns_not_found_if_order_does_not_exist_or_not_belong_to_user()
{
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    // Buat event terlebih dahulu
    $event = Events::factory()->create();

    // Buat order milik user lain, dengan event yang valid
    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
        'event_id' => $event->id,
        'status' => 'completed'
    ]);

    $this->actingAs($user);

    $response = $this->getJson("/api/orders/{$order->order_number}/ticket");

    $response->assertStatus(404);
}

  
 #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_if_order_status_is_not_completed()
    {
        $user = User::factory()->create();
        $event = Events::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending'
        ]);

        $this->actingAs($user);

        $response = $this->getJson("/api/orders/{$order->order_number}/ticket");

        $response->assertStatus(400)
                 ->assertJson([
                    'message' => 'Ticket cannot be printed. Order is not paid.'
                 ]);
    }

}
