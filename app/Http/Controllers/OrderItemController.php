<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderItemController extends Controller
{
    public function index()
    {
        $items = OrderItem::with('order', 'product')->get();
        return response()->json($items, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|string|unique:order_items,id',
            'order_id' => 'required|string|exists:orders,order_id',
            'product_id' => 'required|string|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $item = OrderItem::create($validated);

        return response()->json([
            'message' => 'Order item created successfully.',
            'data' => $item
        ], 201);
    }

    public function show($id)
    {
        $item = OrderItem::with('order', 'product')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        return response()->json($item, 200);
    }

    public function update(Request $request, $id)
    {
        $item = OrderItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        $validated = $request->validate([
            'order_id' => 'required|string|exists:orders,order_id',
            'product_id' => 'required|string|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Order item updated successfully.',
            'data' => $item
        ], 200);
    }

    public function destroy($id)
    {
        $item = OrderItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Order item not found.'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Order item deleted successfully.'], 200);
    }
}
