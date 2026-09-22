<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_id')->unique()->constrained('billing')->cascadeOnDelete();
            $table->string('metode', 30)->default('Tunai');
            $table->integer('uang_dibayar');
            $table->integer('kembalian')->default(0);
            $table->string('no_referensi', 50)->nullable();
            $table->timestamp('tanggal_bayar')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
