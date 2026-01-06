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
// 🟢 PUBLIC ROUTES (โซนลูกค้า - ไม่ต้องล็อกอิน)
// ========================================================================

// 1. เมนูอาหาร & หมวดหมู่ (สำหรับหน้าลูกค้า)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [ProductController::class, 'getCategories']);

// 2. การสั่งอาหาร (สำหรับลูกค้า)
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/table/{id}', [OrderController::class, 'tableHistory']); // ดูประวัติการสั่งของโต๊ะ


// ========================================================================
// 🔒 PROTECTED ROUTES (โซนพนักงาน - ต้องล็อกอิน)
// ========================================================================
// หมายเหตุ: ใช้ middleware 'auth:sanctum' เพื่อตรวจสอบสิทธิ์
Route::middleware('auth:sanctum')->group(function () {

    // 👤 User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🛠️ Product Management (จัดการสินค้า - เพิ่ม/ลบ/แก้ไข)
    Route::post('/products', [ProductController::class, 'store']);
    Route::post('/products/{id}', [ProductController::class, 'update']); // ใช้ POST เพื่อรองรับ File Upload
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // 🍳 Kitchen System (ครัว)
    Route::get('/kitchen/orders', [OrderController::class, 'kitchenOrders']);
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // 🤵 Waiter & Cashier (เด็กเสิร์ฟ & แคชเชียร์)
    Route::get('/waiter/orders', [OrderController::class, 'waiterOrders']);
    Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']);

    // 👑 Admin & Owner Tools (เจ้าของร้าน)
    // - Dashboard Stats
    Route::get('/admin/stats', [AdminController::class, 'getDashboardStats']);

    // - User Management (จัดการพนักงาน)
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::post('/admin/users', [AdminController::class, 'createUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // - Table Management (จัดการโต๊ะ)
    Route::get('/admin/tables', [TableController::class, 'index']);
    Route::post('/admin/tables', [TableController::class, 'store']);
    Route::put('/admin/tables/{id}', [TableController::class, 'update']);
    Route::delete('/admin/tables/{id}', [TableController::class, 'destroy']);

    // - Analytics & Reports (รายงานผลประกอบการ)
    Route::get('/admin/reports/sales', [AdminController::class, 'getSalesChart']);
    Route::get('/admin/reports/products', [AdminController::class, 'getProductPerformance']);

    Route::post('/admin/categories', [ProductController::class, 'createCategory']);
});
