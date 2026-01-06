<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table; // <--- อย่าลืมบรรทัดนี้
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // 1. ลูกค้าสั่งอาหาร (Store)
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // 🛡️ ป้องกันการสั่งจากโต๊ะที่ปิดอยู่
        $table = Table::findOrFail($request->table_id);
        if ($table->status === 'hidden') {
            return response()->json(['message' => 'ขออภัย โต๊ะนี้ปิดให้บริการแล้ว ไม่สามารถสั่งอาหารได้'], 403);
        }

        try {
            // ใช้ Transaction เพื่อความชัวร์ (ถ้าพังให้ Rollback หมด)
            return DB::transaction(function () use ($request) {

                $totalAmount = 0;
                // คำนวณยอดเงินรวม
                foreach ($request->items as $item) {
                    $product = Product::find($item['id']);
                    $totalAmount += $product->price * $item['qty'];
                }

                // สร้าง Order
                $order = Order::create([
                    'table_id' => $request->table_id,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                    'total_amount' => $totalAmount,
                    'status' => 'pending', // สถานะเริ่ม = รอครัว
                    'is_paid' => false
                ]);

                // สร้าง Order Items (รายการอาหาร)
                foreach ($request->items as $item) {
                    $product = Product::find($item['id']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name, // เก็บชื่อไว้กันเปลี่ยน
                        'quantity' => $item['qty'],
                        'price' => $product->price,
                        'subtotal' => $product->price * $item['qty']
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'ส่งออเดอร์เข้าครัวเรียบร้อย!',
                    'order_id' => $order->id
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. ดึงออเดอร์สำหรับห้องครัว (Kitchen)
    public function kitchenOrders()
    {
        $orders = Order::with(['items', 'table'])
            ->whereIn('status', ['pending', 'cooking']) // เอาเฉพาะที่ยังไม่เสร็จ
            ->orderBy('created_at', 'asc') // มาก่อนทำก่อน
            ->get();

        return response()->json($orders);
    }

    // 3. อัปเดตสถานะ (เปลี่ยนเป็น กำลังทำ / เสร็จแล้ว / เสิร์ฟแล้ว)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status
        ]);

        return response()->json(['message' => 'อัปเดตสถานะเรียบร้อย']);
    }

    // 4. ดึงออเดอร์สำหรับเด็กเสิร์ฟ (Waiter)
    public function waiterOrders()
    {
        $orders = Order::with(['items', 'table'])
            ->whereIn('status', ['ready', 'served']) // เสร็จแล้ว หรือ เสิร์ฟแล้ว(รอกิน)
            ->where('is_paid', false) // ยังไม่จ่ายเงิน
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    // 5. เช็คบิล (Checkout)
    public function checkout($id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'status' => 'paid',
            'is_paid' => true
        ]);

        $table = Table::find($order->table_id);
        if($table) {
            $table->status = 'available';
            $table->save();
        }

        return response()->json(['message' => 'เช็คบิลเรียบร้อย ขอบคุณที่ใช้บริการ!']);
    }

    // 6. ข้อมูล Dashboard (สำหรับเจ้าของร้าน)
    public function dashboardStats()
    {
        $today = now()->format('Y-m-d');

        // ยอดขายวันนี้ (เฉพาะที่จ่ายเงินแล้ว)
        $todaySales = Order::whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('total_amount');

        // จำนวนออเดอร์วันนี้
        $todayOrders = Order::whereDate('created_at', $today)->count();

        // ออเดอร์ที่ค้างอยู่ (Active)
        $activeOrders = Order::whereIn('status', ['pending', 'cooking', 'ready', 'served'])->count();

        return response()->json([
            'total_sales' => $todaySales,
            'total_orders' => $todayOrders,
            'active_orders' => $activeOrders
        ]);
    }

    // 7. ดึงประวัติการสั่งของโต๊ะนี้ (Table History)
    public function tableHistory($tableId)
    {
        $orders = Order::with('items')
            ->where('table_id', $tableId)
            ->where('status', '!=', 'paid') // เอาเฉพาะที่ยังกินอยู่
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }
}
