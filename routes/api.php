<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Endpoint dashboard counts
    Route::get('/dashboard/counts', function () {
        return response()->json([
            'users' => \App\Models\User::count(),
            'customers' => \App\Models\Customer::count(),
            'barangs' => \App\Models\Product::count(),
            'categories' => \App\Models\Category::count(), // ✅ Tambahan: kategori
            'orders' => \App\Models\Order::count(),
            'total_pendapatan' => DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.product_id')
                ->select(DB::raw('SUM(order_items.quantity * products.price) as total'))
                ->value('total') ?? 0,
        ]);
    });

    // Resource Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);
});
