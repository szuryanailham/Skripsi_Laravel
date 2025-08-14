<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Events;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTiketTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_download_ticket_pdf()
    {
        // Fake the storage before anything happens
        Storage::fake('public');

        // Arrange: create user and event
        $user = User::factory()->create();
        $event = Events::factory()->create();

        // Act: create an order
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'completed',
            'paid' => true,
        ]);

        // Authenticate user if route requires auth
        $this->actingAs($user);

        // Hit the endpoint
        $response = $this->getJson(route('tiket.download', $order->id));

        // Assert response status and structure
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Tiket berhasil dibuat.',
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'download_url',
        ]);

        // Assert PDF file exists in storage
        $filePath = 'tiket/tiket-' . $order->id . '.pdf';
        Storage::disk('public')->assertExists($filePath);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function download_ticket_should_fail_if_order_not_found()
    {
        // If the route requires auth, we must login a user
        $user = User::factory()->create();
        $this->actingAs($user);

        $invalidOrderId = 9999;

        $response = $this->getJson(route('tiket.download', $invalidOrderId));

        // Jika route menggunakan try-catch, ubah ke 404 atau 422
        $response->assertStatus(500);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
