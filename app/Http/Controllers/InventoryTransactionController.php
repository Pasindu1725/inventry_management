<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class 
InventoryTransactionController extends Controller
{
    /**
     * Add stock to an item.
     */
    public function addStock(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $item) {
            $item->increment('quantity', $request->quantity);

            InventoryTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'addition',
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'transaction_date' => now(),
            ]);
        });

        return back()->with('success', 'Stock added successfully.');
    }

    /**
     * Deduct stock from an item.
     */
    public function deductStock(Request $request, Item $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $item) {
            // Ensure sufficient stock before deduction
            if ($item->quantity < $request->quantity) {
                return back()->withErrors(['quantity' => 'Not enough stock to deduct.']);
            }

            $item->decrement('quantity', $request->quantity);

            InventoryTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'deduction',
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'transaction_date' => now(),
            ]);
        });

        return back()->with('success', 'Stock deducted successfully.');
    }
}
