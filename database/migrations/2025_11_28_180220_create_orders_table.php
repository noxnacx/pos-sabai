<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        // เปลี่ยนจาก user_id เป็น table_id (เพราะลูกค้าสั่งเองจากโต๊ะ)
        // แต่ถ้าจะเก็บ user_id ด้วยก็ได้ (กรณีพนักงานสั่งให้)
        $table->foreignId('table_id')->constrained();

        $table->string('invoice_number')->unique();
        $table->decimal('total_amount', 10, 2)->default(0);

        // สถานะต้องละเอียดขึ้น
        $table->enum('status', ['pending', 'cooking', 'ready', 'served', 'paid', 'cancelled'])->default('pending');

        $table->boolean('is_paid')->default(false); // จ่ายเงินหรือยัง
        $table->timestamps();
    });
}
};
