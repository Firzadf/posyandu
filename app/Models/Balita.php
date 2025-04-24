<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Balita extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'balita';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'posyandu_id',
        'nama_lengkap',
        'nama_panggilan',
        'tanggal_lahir',
        'jenis_kelamin',
        'nik',
        'no_kk',
        'anak_ke',
        'berat_lahir',
        'panjang_lahir',
        'nama_ayah',
        'nama_ibu',
        'no_hp_ortu',
        'alamat',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'foto',
        'catatan',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the posyandu that owns the balita.
     */
    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class);
    }

    /**
     * Get the pemeriksaan for the balita.
     */
    public function pemeriksaan()
    {
        return $this->hasMany(PemeriksaanBalita::class);
    }

    /**
     * Get the pemeriksaan terakhir for the balita.
     */
    public function pemeriksaanTerakhir()
    {
        return $this->hasOne(PemeriksaanBalita::class)->latest('tanggal_pemeriksaan');
    }

    /**
     * Get the pemberian imunisasi for the balita.
     */
    public function pemberianImunisasi()
    {
        return $this->hasMany(PemberianImunisasi::class);
    }

    /**
     * Get the pemberian vitamin for the balita.
     */
    public function pemberianVitamin()
    {
        return $this->hasMany(PemberianVitamin::class);
    }

    /**
     * Get the usia balita dalam bulan.
     *
     * @return int
     */
    public function usiaBulan()
    {
        return $this->tanggal_lahir->diffInMonths(Carbon::now());
    }

    /**
     * Get the usia balita dalam tahun dan bulan.
     *
     * @return string
     */
    public function usiaTahunBulan()
    {
        $tahun = floor($this->usiaBulan() / 12);
        $bulan = $this->usiaBulan() % 12;

        if ($tahun > 0 && $bulan > 0) {
            return "{$tahun} tahun {$bulan} bulan";
        } elseif ($tahun > 0) {
            return "{$tahun} tahun";
        } else {
            return "{$bulan} bulan";
        }
    }

    /**
     * Get the alamat lengkap balita.
     *
     * @return string
     */
    public function alamatLengkap()
    {
        $rt_rw = $this->rt_rw ? "RT/RW {$this->rt_rw}, " : '';
        return "{$this->alamat}, {$rt_rw}{$this->kelurahan}, {$this->kecamatan}, {$this->kota}";
    }

    /**
     * Apakah balita perlu imunisasi.
     *
     * @param Imunisasi $imunisasi
     * @return bool
     */
    public function perluImunisasi(Imunisasi $imunisasi)
    {
        // Cek apakah balita sudah mendapatkan imunisasi ini
        $sudahImunisasi = $this->pemberianImunisasi()
            ->where('imunisasi_id', $imunisasi->id)
            ->exists();

        // Jika belum imunisasi dan usianya sudah mencukupi
        return !$sudahImunisasi && $this->usiaBulan() >= $imunisasi->usia_pemberian;
    }

    /**
     * Apakah balita perlu vitamin.
     *
     * @param Vitamin $vitamin
     * @return bool
     */
    public function perluVitamin(Vitamin $vitamin)
    {
        // Cek kapan terakhir kali balita mendapatkan vitamin ini
        $terakhirVitamin = $this->pemberianVitamin()
            ->where('vitamin_id', $vitamin->id)
            ->orderBy('tanggal_pemberian', 'desc')
            ->first();

        // Jika belum pernah vitamin dan usianya sudah mencukupi
        if (!$terakhirVitamin && $this->usiaBulan() >= $vitamin->usia_pemberian) {
            return true;
        }

        // Jika sudah pernah vitamin, cek apakah sudah 6 bulan sejak pemberian terakhir
        if ($terakhirVitamin) {
            $tanggal_terakhir = Carbon::parse($terakhirVitamin->tanggal_pemberian);
            return $tanggal_terakhir->diffInMonths(Carbon::now()) >= 6;
        }

        return false;
    }
}