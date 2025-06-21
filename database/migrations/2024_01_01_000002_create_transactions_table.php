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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // ID transaksi unik
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('game_user_id'); // ID user dalam game
            $table->string('game_username')->nullable(); // username dalam game
            $table->string('product_code'); // kode produk/item yang dibeli
            $table->string('product_name'); // nama produk
            $table->decimal('amount', 10, 2); // jumlah pembayaran
            $table->string('status')->default('pending'); // pending, success, failed, cancelled
            $table->string('provider'); // apigames, codashop, duniagames
            $table->json('provider_response')->nullable(); // response dari provider
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
