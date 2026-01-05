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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // เช่น "โต๊ะ 1", "โต๊ะ VIP"
            $table->string('qr_code_url')->nullable(); // เก็บลิ้งค์ QR Code
            $table->string('status')->default('available'); // available, occupied
            $table->timestamps();
        });
    }

};
