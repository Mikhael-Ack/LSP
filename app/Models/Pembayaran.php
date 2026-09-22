<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'billing_id',
        'metode',
        'uang_dibayar',
        'kembalian',
        'no_referensi',
        'tanggal_bayar',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }
}
