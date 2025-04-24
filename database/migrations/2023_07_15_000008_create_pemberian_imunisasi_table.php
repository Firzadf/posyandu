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
        Schema::create('pemberian_imunisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->foreignId('imunisasi_id')->constrained('imunisasi')->onDelete('cascade');
            $table->date('tanggal_pemberian');
            $table->string('no_batch')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Kader/bidan yang memberikan imunisasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemberian_imunisasi');
    }
};