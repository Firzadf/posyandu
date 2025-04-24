<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vitamin extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vitamin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_vitamin',
        'kode',
        'usia_pemberian',
        'deskripsi',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the pemberian vitamin for the vitamin.
     */
    public function pemberianVitamin()
    {
        return $this->hasMany(PemberianVitamin::class);
    }

    /**
     * Get the formatted usia pemberian.
     *
     * @return string
     */
    public function usiaFormatted()
    {
        if ($this->usia_pemberian == 0) {
            return 'Saat lahir';
        } elseif ($this->usia_pemberian < 12) {
            return "{$this->usia_pemberian} bulan";
        } else {
            $tahun = floor($this->usia_pemberian / 12);
            $bulan = $this->usia_pemberian % 12;
            
            if ($bulan == 0) {
                return "{$tahun} tahun";
            } else {
                return "{$tahun} tahun {$bulan} bulan";
            }
        }
    }
}