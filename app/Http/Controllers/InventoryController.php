<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        return response()->json(Inventory::with('product')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:inventory',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::create($request->all());
        return response()->json($inventory, 201);
    }

    public function show($id)
    {
        $inventory = Inventory::with('product')->find($id);
        return $inventory ? response()->json($inventory) : response()->json(['error' => 'Inventory not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            $inventory->update($request->all());
            return response()->json($inventory);
        }
        return response()->json(['error' => 'Inventory not found'], 404);
    }

    public function destroy($id)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            $inventory->delete();
            return response()->json(['message' => 'Inventory deleted']);
        }
        return response()->json(['error' => 'Inventory not found'], 404);
    }
}
