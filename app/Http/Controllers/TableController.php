<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // ดึงรายชื่อโต๊ะทั้งหมด
    public function index()
    {
        // เรียงตามชื่อ (หรือ id)
        return response()->json(Table::orderBy('id')->get());
    }

    // เพิ่มโต๊ะใหม่
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:tables']);

        $table = Table::create([
            'name' => $request->name,
            'status' => 'available', // เริ่มต้นเป็นว่าง
            'qr_code_url' => url('/table/') // เก็บ base url ไว้ก่อน
        ]);

        // อัปเดต URL จริงตาม ID (เช่น /table/5)
        $table->update(['qr_code_url' => url('/table/' . $table->id)]);

        return response()->json(['message' => 'เพิ่มโต๊ะสำเร็จ', 'data' => $table]);
    }

    // แก้ไขสถานะ / ชื่อโต๊ะ
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        // ถ้าส่ง status มา ให้แก้ status (available/occupied/hidden)
        if ($request->has('status')) {
            $table->status = $request->status;
        }

        // ถ้าส่ง name มา
        if ($request->has('name')) {
            $table->name = $request->name;
        }

        $table->save();

        return response()->json(['message' => 'อัปเดตเรียบร้อย', 'data' => $table]);
    }

    // ลบโต๊ะ
    public function destroy($id)
    {
        Table::destroy($id);
        return response()->json(['message' => 'ลบโต๊ะสำเร็จ']);
    }
}
