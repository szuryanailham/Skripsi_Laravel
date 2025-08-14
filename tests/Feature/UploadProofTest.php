<?php

namespace Tests\Feature;

use App\Models\Events;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function PHPUnit\Framework\assertFileExists;

class UploadProofTest extends TestCase
{
    use RefreshDatabase;

     #[\PHPUnit\Framework\Attributes\Test]


public function test_user_can_upload_payment_proof()
{
    // Fake storage
    Storage::fake('public');

    // Buat user dan event terlebih dahulu
    $user = User::factory()->create();
    $event = Events::factory()->create();

    // Buat order terkait user dan event
    $order = Order::create([
        'order_number' => 'ORD-' . rand(1000000, 9999999),
        'user_id' => $user->id,
        'event_id' => $event->id,
        'total_amount' => 316.97,
        'status' => 'completed',
        'paid' => false,
    ]);

    // Login user (jika route dilindungi auth)
    $this->actingAs($user);

    // Simulasi file image
    $file = UploadedFile::fake()->image('bukti.jpg');

    // Kirim request upload bukti
    $response = $this->postJson("/api/orders/{$order->id}/upload-proof", [
        'proof_image' => $file
    ]);

    // Cek response sukses
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Bukti pembayaran berhasil diunggah.'
    ]);

    // Cek file tersimpan
    Storage::disk('public')->assertExists($order->fresh()->proof_image);
}


   
}
