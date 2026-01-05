<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TableController;
use App\Models\Table; // <--- 1. เพิ่มบรรทัดนี้เพื่อเรียกใช้ Model Table

// 🟢 โซนลูกค้า (เข้าได้เฉพาะโต๊ะที่เปิดอยู่)
Route::get('/', function () { return view('welcome'); });

Route::get('/table/{id}', function ($id) {
    // 2. เช็คสถานะโต๊ะก่อน
    $table = Table::findOrFail($id);

    if ($table->status === 'hidden') {
        // ถ้าปิดอยู่ ให้ไปหน้าแจ้งเตือน
        return response()->view('table_closed', [], 403);
    }

    return view('customer_order', ['table_id' => $id]);
});

// 🔒 โซนพนักงาน (เหมือนเดิม)
Route::middleware('auth')->group(function () {
    Route::get('/kitchen', function () { return view('kitchen'); });
    Route::get('/waiter', function () { return view('waiter'); });
    Route::get('/admin/menu', function () { return view('admin_menu'); });
    Route::get('/admin/dashboard', function () { return view('admin_dashboard'); });
    Route::get('/admin/users', function () { return view('admin_users'); });
    Route::get('/admin/tables', function () { return view('admin_tables'); });
    Route::get('/admin/reports', function () { return view('admin_reports'); });
});

// 🔑 โซนล็อกอิน (เหมือนเดิม)
Route::get('/login', function () { return view('login'); })->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
