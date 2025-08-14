<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Events;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;


class ManageOrderByAdminTest  extends TestCase
{
    use RefreshDatabase;



public function test_admin_can_retrieve_orders()
{
    // Buat admin
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    Sanctum::actingAs($admin, ['*']);


    $orders = Order::factory()
        ->count(3)
        ->for(User::factory(), 'user')   
        ->for(Events::factory(), 'event') 
        ->create();

    $response = $this->getJson('/api/admin/orders');

    $response->assertStatus(200)
             ->assertJsonCount(3) // pastikan ada 3 data
             ->assertJsonStructure([
                 '*' => [
                     'id',
                     'user' => [
                         'id', 'name', 'email'
                     ],
                     'event' => [
                         'id', 'title', 'slug'
                     ],
                     'created_at',
                     'updated_at'
                 ]
             ]);
}

public function test_admin_can_verify_order()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();
    $event = Events::factory()->create();

    Sanctum::actingAs($admin, ['*']);

    $order = Order::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => 'pending',
    ]);

    $response = $this->putJson("/api/orders/{$order->id}/verification");

    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Order verified successfully',
                 'order' => [
                     'id' => $order->id,
                     'status' => 'completed',
                 ],
             ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'completed',
    ]);
}

public function test_admin_gets_error_when_verifying_non_existing_order()
{
    $admin = User::factory()->create(['role' => 'admin']);

    Sanctum::actingAs($admin, ['*']);

    // Gunakan ID order yang tidak ada
    $nonExistingOrderId = 9999;

    $response = $this->putJson("/api/orders/{$nonExistingOrderId}/verification");

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Order not found or verification failed.',
             ]);
}

public function test_non_admin_cannot_verify_order()
{
    $user = User::factory()->create(['role' => 'user']);
    $event = Events::factory()->create();

    // Buat order milik user biasa
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'status' => 'pending',
    ]);

    // Login sebagai user biasa (bukan admin)
    Sanctum::actingAs($user, ['*']);

    $response = $this->putJson("/api/orders/{$order->id}/verification");

    // Karena route pakai middleware isAdmin, harusnya ditolak
    $response->assertStatus(403)
             ->assertJson([
                 'message' => 'Access Denied. Admins only.',
             ]);
}



}
