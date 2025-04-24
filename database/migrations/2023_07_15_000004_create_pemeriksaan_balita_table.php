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
        Schema::create('pemeriksaan_balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balita_id')->constrained('balita')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->float('berat_badan', 5, 2); // dalam kg
            $table->float('tinggi_badan', 5, 2); // dalam cm
            $table->float('lingkar_kepala', 5, 2)->nullable(); // dalam cm
            $table->float('lingkar_lengan', 5, 2)->nullable(); // dalam cm
            $table->string('status_gizi')->nullable(); // normal, kurang, buruk, lebih
            $table->text('keluhan')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Kader yang melakukan pemeriksaan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_balita');
    }
};