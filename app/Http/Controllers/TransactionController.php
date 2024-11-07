<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(Transaction::with(['product', 'user'])->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
        ]);

        $transaction = Transaction::create($request->all());
        return response()->json($transaction, 201);
    }

    public function show($id)
    {
        $transaction = Transaction::with(['product', 'user'])->find($id);
        return $transaction ? response()->json($transaction) : response()->json(['error' => 'Transaction not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->update($request->all());
            return response()->json($transaction);
        }
        return response()->json(['error' => 'Transaction not found'], 404);
    }

    public function destroy($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            $transaction->delete();
            return response()->json(['message' => 'Transaction deleted']);
        }
        return response()->json(['error' => 'Transaction not found'], 404);
    }
}
