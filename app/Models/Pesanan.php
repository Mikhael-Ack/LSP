<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'no_pesanan',
        'no_meja',
        'tanggal',
        'status_pesanan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function billing()
    {
        return $this->hasOne(Billing::class, 'pesanan_id');
    }
}
