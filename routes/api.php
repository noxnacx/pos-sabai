<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TableController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========================================================================
// 🟢 PUBLIC ROUTES (โซนลูกค้า - เข้าถึงได้โดยไม่ต้องล็อกอิน)
// ========================================================================

// 1. ระบบยืนยันตัวตน (Login)
Route::post('/login', [AuthController::class, 'login']);

// 2. ข้อมูลร้านค้า (โต๊ะ, เมนู)
// ✨ สำคัญ: ต้องเปิดให้ลูกค้าเช็คสถานะโต๊ะได้โดยไม่ต้องล็อกอิน
Route::get('/tables/{id}', [TableController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [ProductController::class, 'getCategories']);

// 3. ระบบสั่งอาหาร (Customer Order)
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/table/{id}', [OrderController::class, 'tableHistory']); // ดูรายการที่สั่งไปแล้ว


// ========================================================================
// 🔒 PROTECTED ROUTES (โซนพนักงาน/ผู้จัดการ - ต้องล็อกอิน)
// ========================================================================
Route::middleware('auth:sanctum')->group(function () {

    // 🚪 Logout & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 📦 Product Management (จัดการสินค้า)
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']); // ใช้ POST แทน PUT เพื่อรองรับ FormData/Image
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/admin/categories', [ProductController::class, 'createCategory']);
    Route::post('/admin/categories/reorder', [ProductController::class, 'reorderCategories']);

    // 🪑 Table Management (จัดการโต๊ะ - ฝั่ง Admin)
    Route::get('/admin/tables', [TableController::class, 'index']);
    Route::post('/admin/tables', [TableController::class, 'store']);
    Route::put('/admin/tables/{id}', [TableController::class, 'update']); // อัปเดตสถานะโต๊ะ (เปิด/ปิด)
    Route::delete('/admin/tables/{id}', [TableController::class, 'destroy']);
    Route::delete('/admin/categories/{id}', [ProductController::class, 'deleteCategory']);

    // 🍳 Kitchen System (ครัว)
    Route::get('/kitchen/orders', [OrderController::class, 'kitchenOrders']);
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']); // อัปเดตสถานะอาหาร (cooking/ready)

    // 🤵 Waiter & Cashier (เด็กเสิร์ฟ & แคชเชียร์)
    Route::get('/waiter/orders', [OrderController::class, 'waiterOrders']);
    Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']); // เช็คบิล/ปิดโต๊ะ

    // 👑 Admin & User Management (จัดการผู้ใช้งาน)
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::post('/admin/users', [AdminController::class, 'createUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // 📊 Dashboard & Reports (รายงาน)
    Route::get('/admin/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/admin/reports/sales', [AdminController::class, 'getSalesChart']);
    Route::get('/admin/reports/products', [AdminController::class, 'getProductPerformance']);

});
