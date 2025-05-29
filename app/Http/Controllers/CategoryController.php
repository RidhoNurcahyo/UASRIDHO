<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all(), 200);
    }

    public function store(Request $request)
    {
        // Validasi: category_id boleh null, tapi jika ada harus unique & string
        $validated = $request->validate([
            'category_id' => 'nullable|string|unique:categories,category_id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        // Jika category_id kosong, buat UUID otomatis
        if (empty($validated['category_id'])) {
            $validated['category_id'] = (string) Str::uuid();
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category, 200);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json($category, 200);
    }

    public function destroy(Category $category)
    {
        $deleted = $category->delete();

        return response()->json(['deleted' => (bool) $deleted], $deleted ? 200 : 400);
    }
}
