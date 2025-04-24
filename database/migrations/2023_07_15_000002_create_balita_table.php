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
        Schema::create('balita', function (Blueprint $table) {
            $table->id();
            $table->foreignId('posyandu_id')->constrained('posyandu')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nik')->unique();
            $table->string('no_kk');
            $table->string('anak_ke')->nullable();
            $table->string('berat_lahir')->nullable();
            $table->string('panjang_lahir')->nullable();
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('no_hp_ortu')->nullable();
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
        Schema::dropIfExists('balita');
    }
};