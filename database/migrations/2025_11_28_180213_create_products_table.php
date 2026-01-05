<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained(); // ผูกกับ categories
        $table->string('name');
        $table->string('sku')->unique()->nullable(); // รหัสบาร์โค้ด
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2); // ราคาขาย
        $table->decimal('cost_price', 10, 2)->nullable(); // ต้นทุน
        $table->integer('stock_quantity')->default(0);
        $table->string('image_url')->nullable();
        $table->jsonb('attributes')->nullable(); // เก็บ สี, ไซส์ (Postgres feature!)
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
};
