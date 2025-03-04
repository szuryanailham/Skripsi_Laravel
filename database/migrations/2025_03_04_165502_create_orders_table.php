<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Nomor pesanan unik
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke tabel users
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); // Relasi ke tabel events
            $table->decimal('total_amount', 10, 2); // Jumlah total pesanan
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending'); // Status order
            $table->boolean('paid')->default(false); // Status pembayaran
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran jika sudah dibayar
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
