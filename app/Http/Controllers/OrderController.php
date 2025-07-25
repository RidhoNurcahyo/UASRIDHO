<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan.
     */
    public function index(): JsonResponse
    {
        $orders = Order::with(['customer', 'orderItems.product'])->get();
        return response()->json($orders);
    }

    /**
     * Menyimpan pesanan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'string', 'max:50', 'unique:orders,order_id'],
            'customer_id' => ['required', 'string', 'max:50', 'exists:customers,customer_id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,completed,cancelled,shipped'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'string', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ], [
            'customer_id.exists' => 'ID pelanggan yang dipilih tidak ada di tabel customers.',
            'items.*.product_id.exists' => 'ID produk yang dipilih tidak ada di tabel products.',
            'order_id.unique' => 'ID pesanan sudah digunakan.',
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = collect($validated['items'])->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'order_id' => $validated['order_id'],
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
            ]);

            foreach ($validated['items'] as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();
            return response()->json($order->load(['orderItems.product', 'customer']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail pesanan tertentu.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $order = Order::with(['customer', 'orderItems.product'])->findOrFail($id);
            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }
    }

    /**
     * Memperbarui pesanan tertentu.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'customer_id' => ['required', 'string', 'max:50', 'exists:customers,customer_id'],
                'order_date' => ['required', 'date'],
                'status' => ['required', 'in:pending,completed,cancelled,shipped'],
            ], [
                'customer_id.exists' => 'ID pelanggan yang dipilih tidak ada di tabel customers.',
            ]);

            $order->update($validated);
            return response()->json($order->load(['orderItems.product', 'customer']));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }
    }

    /**
     * Menghapus pesanan tertentu.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            $order->orderItems()->delete();
            $order->delete();
            return response()->json(['message' => 'Pesanan berhasil dihapus'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }
    }
}