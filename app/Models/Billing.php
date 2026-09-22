<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing';
    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'no_tagihan',
        'total_bayar',
        'pajak',
        'grand_total',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'billing_id');
    }
}
