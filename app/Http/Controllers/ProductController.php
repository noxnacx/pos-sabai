<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ฟังก์ชันสำหรับดึงสินค้าทั้งหมด
    public function index()
    {
        // สั่งให้ดึงสินค้าทั้งหมด + เอาข้อมูลหมวดหมู่มาด้วย (with category)
        // และเอาเฉพาะตัวที่ is_active = true
        $products = Product::with('category')
                    ->where('is_active', true)
                    ->get();

        // ส่งกลับไปเป็น JSON (รูปแบบมาตรฐานของ API)
        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'data' => $products
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // 1. เพิ่มสินค้าใหม่ (พร้อมรูป)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'description' => $request->description, // ✅ เพิ่มบรรทัดนี้
            'image_url' => $imagePath ? '/storage/' . $imagePath : null,
            'is_active' => true
        ]);

        return response()->json(['message' => 'เพิ่มเมนูสำเร็จ', 'data' => $product]);
    }

    // 2. ลบสินค้า
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete(); // ลบออกจาก DB (รูปยังอยู่ ถือว่าเก็บประวัติ)
        return response()->json(['message' => 'ลบเมนูเรียบร้อย']);
    }

    // 3. ดึงหมวดหมู่ทั้งหมด (เอาไว้ใส่ใน Dropdown ตอนเพิ่มสินค้า)
    public function getCategories()
    {
        // เพิ่ม ->orderBy('sort_order', 'asc')
        return \App\Models\Category::orderBy('sort_order', 'asc')->get();
    }

    public function reorderCategories(Request $request)
    {
        $categories = $request->categories; // รับ array ที่เรียงมาแล้วจาก frontend

        foreach ($categories as $index => $cat) {
            \App\Models\Category::where('id', $cat['id'])->update(['sort_order' => $index + 1]);
        }

        return response()->json(['message' => 'บันทึกลำดับเรียบร้อย']);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image_url = '/storage/' . $imagePath;
        }

        // ✅ เพิ่ม description ในการอัปเดต
        $product->update($request->only(['name', 'price', 'category_id', 'is_active', 'description']));

        return response()->json(['message' => 'อัปเดตสำเร็จ', 'data' => $product]);
    }

    public function createCategory(Request $request)
    {
        $request->validate(['name' => 'required|unique:categories']);
        $category = \App\Models\Category::create([
            'name' => $request->name,
            'is_active' => true
        ]);
        return response()->json($category);
    }

    public function deleteCategory($id)
    {
        // เช็คว่ามีสินค้าใช้อยู่ไหม
        $isInUse = Product::where('category_id', $id)->exists();
        if ($isInUse) {
            return response()->json(['message' => 'ไม่สามารถลบได้ เนื่องจากมีเมนูอาหารอยู่ในหมวดนี้'], 400);
        }

        $category = \App\Models\Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'ลบหมวดหมู่เรียบร้อย']);
    }

    public function toggleStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // รับค่า is_active (0 หรือ 1)
        $product->is_active = $request->is_active;
        $product->save();

        return response()->json([
            'message' => 'อัปเดตสถานะเรียบร้อย',
            'data' => $product
        ]);
    }
}
