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
        Schema::create('pemberian_vitamin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->foreignId('vitamin_id')->constrained('vitamin')->onDelete('cascade');
            $table->date('tanggal_pemberian');
            $table->string('no_batch')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Kader/bidan yang memberikan vitamin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemberian_vitamin');
    }
};