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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // kode game seperti 'mobilelegend', 'freefire'
            $table->string('name'); // nama game
            $table->string('provider'); // apigames, codashop, duniagames
            $table->string('category')->default('game'); // kategori: game, voucher, dll
            $table->json('fields'); // field yang diperlukan untuk cek ID (user_id, zone_id, dll)
            $table->string('icon')->nullable(); // URL icon game
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
