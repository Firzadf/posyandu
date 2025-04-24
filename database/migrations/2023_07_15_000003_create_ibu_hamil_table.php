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
        Schema::create('ibu_hamil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained('posyandu')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('nik')->unique();
            $table->date('tanggal_lahir');
            $table->date('hpht'); // Hari Pertama Haid Terakhir
            $table->date('hpl')->nullable(); // Hari Perkiraan Lahir
            $table->string('golongan_darah')->nullable();
            $table->integer('usia_kehamilan');
            $table->integer('kehamilan_ke');
            $table->string('tinggi_badan')->nullable();
            $table->string('berat_badan_sebelum_hamil')->nullable();
            $table->string('riwayat_penyakit')->nullable();
            $table->string('nama_suami')->nullable();
            $table->string('no_hp');
            $table->text('alamat');
            $table->string('rt_rw')->nullable();
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kota');
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibu_hamil');
    }
};