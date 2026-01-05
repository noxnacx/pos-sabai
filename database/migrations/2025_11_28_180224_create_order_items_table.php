<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // ลบบิล = ลบรายการด้วย
        $table->foreignId('product_id')->constrained();
        $table->string('product_name'); // เก็บชื่อสินค้าไว้ด้วย กันสินค้าเปลี่ยนชื่อ
        $table->integer('quantity');
        $table->decimal('price', 10, 2); // ราคา ณ ตอนขาย
        $table->decimal('subtotal', 10, 2); // quantity * price
        $table->timestamps();
    });
}
};
