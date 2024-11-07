<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('products')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::with('products')->find($id);
        return $category ? response()->json($category) : response()->json(['error' => 'Category not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->update($request->all());
            return response()->json($category);
        }
        return response()->json(['error' => 'Category not found'], 404);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json(['message' => 'Category deleted']);
        }
        return response()->json(['error' => 'Category not found'], 404);
    }

    public function addProductToCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $category = Category::find($request->category_id);
        $product = Product::find($request->product_id);

        if ($category && $product) {
            $category->products()->syncWithoutDetaching($product);
            return response()->json(['message' => 'Product added to category']);
        }

        return response()->json(['error' => 'Category or Product not found'], 404);
    }

    public function removeProductFromCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $category = Category::find($request->category_id);
        $product = Product::find($request->product_id);

        if ($category && $product) {
            $category->products()->detach($product);
            return response()->json(['message' => 'Product removed from category']);
        }

        return response()->json(['error' => 'Category or Product not found'], 404);
    }
}
