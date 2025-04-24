<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Posyandu;
use App\Models\Imunisasi;
use App\Models\Vitamin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat User Admin
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@posyandu.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'no_hp' => '081234567890',
        ]);

        // Membuat User Kader
        User::create([
            'nama' => 'Kader Posyandu',
            'email' => 'kader@posyandu.test',
            'password' => Hash::make('password'),
            'role' => 'kader',
            'no_hp' => '081234567891',
        ]);

        // Membuat User Bidan
        User::create([
            'nama' => 'Bidan Posyandu',
            'email' => 'bidan@posyandu.test',
            'password' => Hash::make('password'),
            'role' => 'bidan',
            'no_hp' => '081234567892',
        ]);

        // Membuat data Posyandu
        Posyandu::create([
            'nama_posyandu' => 'Posyandu Melati',
            'alamat' => 'Jl. Raya Melati No. 123',
            'kelurahan' => 'Melati',
            'kecamatan' => 'Kemuning',
            'kota' => 'Jakarta Timur',
            'kode_pos' => '13910',
            'telepon' => '021-12345678',
            'email' => 'info@posyandumelati.com',
            'deskripsi' => 'Posyandu Melati didirikan tahun 2010 untuk melayani kesehatan ibu dan anak di wilayah Melati.'
        ]);

        // Membuat data Imunisasi
        $imunisasi = [
            [
                'nama_imunisasi' => 'BCG', 
                'kode' => 'BCG', 
                'usia_pemberian' => 0, 
                'deskripsi' => 'Imunisasi untuk mencegah TBC'
            ],
            [
                'nama_imunisasi' => 'Hepatitis B', 
                'kode' => 'HB0', 
                'usia_pemberian' => 0, 
                'deskripsi' => 'Imunisasi untuk mencegah Hepatitis B'
            ],
            [
                'nama_imunisasi' => 'Polio 1', 
                'kode' => 'Polio1', 
                'usia_pemberian' => 2, 
                'deskripsi' => 'Imunisasi untuk mencegah Polio'
            ],
            [
                'nama_imunisasi' => 'DPT-HB-Hib 1', 
                'kode' => 'Pentavalen1', 
                'usia_pemberian' => 2, 
                'deskripsi' => 'Imunisasi untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b'
            ],
            [
                'nama_imunisasi' => 'Polio 2', 
                'kode' => 'Polio2', 
                'usia_pemberian' => 3, 
                'deskripsi' => 'Imunisasi untuk mencegah Polio'
            ],
            [
                'nama_imunisasi' => 'DPT-HB-Hib 2', 
                'kode' => 'Pentavalen2', 
                'usia_pemberian' => 3, 
                'deskripsi' => 'Imunisasi untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b'
            ],
            [
                'nama_imunisasi' => 'Polio 3', 
                'kode' => 'Polio3', 
                'usia_pemberian' => 4, 
                'deskripsi' => 'Imunisasi untuk mencegah Polio'
            ],
            [
                'nama_imunisasi' => 'DPT-HB-Hib 3', 
                'kode' => 'Pentavalen3', 
                'usia_pemberian' => 4, 
                'deskripsi' => 'Imunisasi untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b'
            ],
            [
                'nama_imunisasi' => 'IPV', 
                'kode' => 'IPV', 
                'usia_pemberian' => 4, 
                'deskripsi' => 'Imunisasi Polio suntik'
            ],
            [
                'nama_imunisasi' => 'Campak', 
                'kode' => 'Campak', 
                'usia_pemberian' => 9, 
                'deskripsi' => 'Imunisasi untuk mencegah Campak'
            ],
            [
                'nama_imunisasi' => 'DPT-HB-Hib Lanjutan', 
                'kode' => 'PentavalenLanjutan', 
                'usia_pemberian' => 18, 
                'deskripsi' => 'Imunisasi lanjutan untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan Haemophilus influenzae type b'
            ],
            [
                'nama_imunisasi' => 'Campak Lanjutan', 
                'kode' => 'CampakLanjutan', 
                'usia_pemberian' => 18, 
                'deskripsi' => 'Imunisasi lanjutan untuk mencegah Campak'
            ]
        ];

        foreach ($imunisasi as $data) {
            Imunisasi::create($data);
        }

        // Membuat data Vitamin
        $vitamin = [
            [
                'nama_vitamin' => 'Vitamin A (Biru)', 
                'kode' => 'VitA-Biru', 
                'usia_pemberian' => 6, 
                'deskripsi' => 'Vitamin A dosis 100.000 IU untuk bayi usia 6-11 bulan'
            ],
            [
                'nama_vitamin' => 'Vitamin A (Merah)', 
                'kode' => 'VitA-Merah', 
                'usia_pemberian' => 12, 
                'deskripsi' => 'Vitamin A dosis 200.000 IU untuk anak usia 12-59 bulan'
            ],
        ];

        foreach ($vitamin as $data) {
            Vitamin::create($data);
        }
    }
}