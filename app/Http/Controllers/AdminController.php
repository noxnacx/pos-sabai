<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // --- 1. Dashboard Stats (ยอดขาย) ---
    public function getDashboardStats()
    {
        $today = now()->format('Y-m-d');

        // ยอดขายวันนี้ (เฉพาะที่จ่ายเงินแล้ว)
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('total_amount');

        // จำนวนออเดอร์วันนี้
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // ออเดอร์ที่ค้างอยู่ (ยังกินไม่เสร็จ)
        $activeOrders = Order::whereIn('status', ['pending', 'cooking', 'ready', 'served'])->count();

        // สินค้าขายดี 5 อันดับแรก
        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return response()->json([
            'today_sales' => $todaySales,
            'today_orders' => $todayOrders,
            'active_orders' => $activeOrders,
            'top_products' => $topProducts
        ]);
    }

    // --- 2. User Management (จัดการพนักงาน) ---
    public function getUsers()
    {
        return response()->json(User::all());
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'เพิ่มพนักงานเรียบร้อย', 'user' => $user]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username,' . $id,
            'role' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'แก้ไขข้อมูลเรียบร้อย', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'ลบพนักงานเรียบร้อย']);
    }

    // --- 3. Reports (รายงานผลประกอบการ) ---

    // ยอดขายย้อนหลัง (Sales Chart)
    public function getSalesChart(Request $request)
    {
        $period = $request->query('period', 'daily'); // daily, monthly

        $query = Order::where('status', 'paid');

        if ($period === 'daily') {
            // ย้อนหลัง 7 วัน
            $query->where('created_at', '>=', now()->subDays(7));
            $select = DB::raw("TO_CHAR(created_at, 'YYYY-MM-DD') as date"); // ใช้ TO_CHAR สำหรับ Postgres
            $groupBy = 'date';
        } elseif ($period === 'monthly') {
            // ย้อนหลัง 12 เดือน
            $query->where('created_at', '>=', now()->subMonths(12));
            $select = DB::raw("TO_CHAR(created_at, 'YYYY-MM') as date");
            $groupBy = 'date';
        }

        $sales = $query->select($select, DB::raw('SUM(total_amount) as total'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
            ->get();

        return response()->json($sales);
    }

    // รายงานสินค้าขายดี / ขายไม่ดี (Product Performance)
    public function getProductPerformance()
    {
        // 1. ขายดี (Top 10) - Logic เดิม (ดึงจากยอดขายจริง)
        $bestSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // 2. ขายไม่ดี / ควรปรับปรุง (Bottom 10) - ✅ แก้ไขใหม่
        // เปลี่ยนมาดึงจาก Products เป็นหลัก เพื่อให้สินค้าที่ยอดขาย 0 ติดมาด้วย
        $lowSellers = DB::table('products')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->where('orders.status', 'paid'); // นับเฉพาะที่จ่ายเงินแล้ว
            })
            ->where('products.is_active', true) // เอาเฉพาะสินค้าที่ยังขายอยู่ (Active)
            ->select(
                'products.name as product_name',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_qty') // ถ้าไม่มีออเดอร์ ให้เป็น 0
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'asc') // เรียงจากน้อยไปมาก (0 จะขึ้นก่อน)
            ->limit(10)
            ->get();

        return response()->json([
            'best_sellers' => $bestSellers,
            'low_sellers' => $lowSellers
        ]);
    }
}
