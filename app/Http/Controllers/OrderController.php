<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('customer', 'items.product')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|unique:orders,order_id',
            'customer_id' => 'required|string|exists:customers,customer_id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|string|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $order = Order::create([
                'order_id' => $validated['order_id'],
                'customer_id' => $validated['customer_id'],
                'order_date' => $validated['order_date'],
                'total_amount' => $totalAmount,
                'status' => $validated['status']
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);
            }

            DB::commit();
            return response()->json($order->load('items.product', 'customer'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Order::with('customer', 'items.product')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|string|exists:customers,customer_id',
            'order_date' => 'required|date',
            'status' => 'required|string'
        ]);

        $order->update($validated);
        return $order;
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.']);
    }
}
