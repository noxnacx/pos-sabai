<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Table; // <--- เรียกใช้ Model โต๊ะ
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. สร้างโต๊ะอาหาร 10 โต๊ะ ---
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'name' => 'Table ' . $i,
                'qr_code_url' => 'http://localhost:8000/table/' . $i, // ลิงก์จำลอง
                'status' => 'available'
            ]);
        }

        // --- 2. สร้างหมวดหมู่ ---
        $foodCategory = Category::create(['name' => 'อาหารจานหลัก', 'is_active' => true]);
        $drinkCategory = Category::create(['name' => 'เครื่องดื่ม', 'is_active' => true]);
        $snackCategory = Category::create(['name' => 'ทานเล่น', 'is_active' => true]);

        // --- 3. สร้างสินค้า (เมนูอาหาร) ---
        // อาหาร
        Product::create([
            'category_id' => $foodCategory->id,
            'name' => 'ข้าวกะเพราหมูสับไข่ดาว',
            'price' => 60.00,
            'stock_quantity' => 100,
            'image_url' => 'https://images.unsplash.com/photo-1590233987063-899477002013?w=500', // รูปตัวอย่างจากเน็ต
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $foodCategory->id,
            'name' => 'สปาเก็ตตี้คาโบนาร่า',
            'price' => 129.00,
            'stock_quantity' => 50,
            'is_active' => true
        ]);

        // เครื่องดื่ม
        Product::create([
            'category_id' => $drinkCategory->id,
            'name' => 'น้ำเปล่า',
            'price' => 15.00,
            'stock_quantity' => 200,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $drinkCategory->id,
            'name' => 'โค้ก (กระป๋อง)',
            'price' => 25.00,
            'stock_quantity' => 100,
            'is_active' => true
        ]);

        // ทานเล่น
        Product::create([
            'category_id' => $snackCategory->id,
            'name' => 'เฟรนช์ฟรายส์',
            'price' => 59.00,
            'stock_quantity' => 50,
            'is_active' => true
        ]);
    }
}
