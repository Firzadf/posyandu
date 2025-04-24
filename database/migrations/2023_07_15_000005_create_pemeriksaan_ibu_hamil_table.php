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
        Schema::create('pemeriksaan_ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_hamil_id')->constrained('ibu_hamil')->onDelete('cascade');
            $table->date('tanggal_pemeriksaan');
            $table->integer('usia_kehamilan'); // dalam minggu
            $table->float('berat_badan', 5, 2); // dalam kg
            $table->integer('tekanan_darah_sistolik'); // dalam mmHg
            $table->integer('tekanan_darah_diastolik'); // dalam mmHg
            $table->integer('tinggi_fundus')->nullable(); // dalam cm
            $table->string('denyut_jantung_janin')->nullable();
            $table->string('status_gizi')->nullable(); // normal, KEK (Kurang Energi Kronis), obesitas
            $table->string('resiko_kehamilan')->nullable(); // normal, resiko tinggi
            $table->text('keluhan')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users'); // Kader/bidan yang melakukan pemeriksaan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_ibu_hamil');
    }
};