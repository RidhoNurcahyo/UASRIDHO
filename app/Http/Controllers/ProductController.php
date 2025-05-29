<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|string|unique:products,product_id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|string|exists:categories,category_id',
        ]);

        if (empty($validated['product_id'])) {
            $validated['product_id'] = (string) Str::uuid();
        }

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'), 200);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|string|exists:categories,category_id',
        ]);

        $product->update($validated);

        return response()->json($product->load('category'), 200);
    }

    public function destroy(Product $product)
    {
        $deleted = $product->delete();
        return response()->json(['deleted' => (bool) $deleted], $deleted ? 200 : 400);
    }
}
