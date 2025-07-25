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
        // Pastikan tabel customers ada sebelum membuat foreign key
        if (Schema::hasTable('customers')) {
            Schema::create('orders', function (Blueprint $table) {
                // Primary key: ID unik untuk pesanan
                $table->string('order_id', 50)->primary();

                // Foreign key: Merujuk ke customer_id di tabel customers
                $table->string('customer_id', 50);
                $table->foreign('customer_id')
                      ->references('customer_id')
                      ->on('customers')
                      ->onDelete('cascade');

                // Tanggal pesanan dengan indeks untuk pencarian cepat
                $table->date('order_date')->index();

                // Total harga dengan presisi untuk nilai moneter
                $table->decimal('total_amount', 10, 2);

                // Status pesanan dengan nilai yang telah ditentukan
                $table->enum('status', ['pending', 'completed', 'cancelled', 'shipped'])
                      ->default('pending');

                // Kolom created_at dan updated_at
                $table->timestamps();
            });
        }
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};