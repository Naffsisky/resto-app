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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('harga');
            $table->string('gambar')->nullable();
            $table->string('variant')->nullable();
            $table->unsignedBigInteger('kategori_id'); // Foreign key ke kategori
            $table->unsignedBigInteger('ukuran_id'); // Foreign key ke ukuran
            $table->boolean('tersedia')->default(true);
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('ukuran_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
