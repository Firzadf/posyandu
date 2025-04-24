<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemberianVitamin extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemberian_vitamin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balita_id',
        'vitamin_id',
        'tanggal_pemberian',
        'no_batch',
        'catatan',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_pemberian' => 'date',
    ];

    /**
     * Get the balita that owns the pemberian vitamin.
     */
    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }

    /**
     * Get the vitamin that owns the pemberian vitamin.
     */
    public function vitamin()
    {
        return $this->belongsTo(Vitamin::class);
    }

    /**
     * Get the user that performed the pemberian vitamin.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung usia balita saat pemberian vitamin dalam bulan.
     *
     * @return int
     */
    public function usiaBalitaSaatVitamin()
    {
        return $this->balita->tanggal_lahir->diffInMonths($this->tanggal_pemberian);
    }

    /**
     * Check if vitamin was given on time.
     *
     * @return bool
     */
    public function tepatWaktu()
    {
        $usia_vitamin = $this->vitamin->usia_pemberian;
        $usia_balita = $this->usiaBalitaSaatVitamin();
        
        // Untuk vitamin, toleransi 2 bulan dari jadwal
        return $usia_balita >= $usia_vitamin && $usia_balita <= ($usia_vitamin + 2);
    }
}