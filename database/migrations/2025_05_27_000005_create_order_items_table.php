<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // Pastikan tabel orders dan products sudah ada
        if (Schema::hasTable('orders') && Schema::hasTable('products')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();

                $table->string('order_id', 50);
                $table->foreign('order_id')
                      ->references('order_id')
                      ->on('orders')
                      ->onDelete('cascade'); // OK

                $table->string('product_id', 50);
                $table->foreign('product_id')
                      ->references('product_id')
                      ->on('products')
                      ->onDelete('cascade'); // ✅ PERBAIKI INI: restrict -> cascade

                $table->unsignedInteger('quantity');
                $table->decimal('price', 10, 2);

                $table->timestamps();
            });
        }
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
