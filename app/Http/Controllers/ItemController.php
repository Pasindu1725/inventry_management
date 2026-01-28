<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MeasurementUnit;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $items = Item::with('measurementUnit')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Items/Index', [
            'items' => $items,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $measurementUnits = MeasurementUnit::all();
        return Inertia::render('Items/Create', [
            'measurementUnits' => $measurementUnits,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
        ]);

        DB::transaction(function () use ($request) {
            $item = Item::create([
                'name' => $request->name,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'measurement_unit_id' => $request->measurement_unit_id,
            ]);

            // Record initial inventory addition
            InventoryTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'addition',
                'quantity' => $request->quantity,
                'notes' => 'Initial stock addition.',
                'transaction_date' => now(),
            ]);
        });

        return to_route('items.index')->with('success', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item->load(['measurementUnit', 'inventoryTransactions.user']);
        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        // Not part of initial requirements, but good to have for resource controllers
        $measurementUnits = MeasurementUnit::all();
        return Inertia::render('Items/Edit', [
            'item' => $item->load('measurementUnit'),
            'measurementUnits' => $measurementUnits,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        // Not part of initial requirements, but good to have for resource controllers
        $request->validate([
            'name' => 'required|string|max:255|unique:items,name,' . $item->id,
            'description' => 'nullable|string',
            'measurement_unit_id' => 'required|exists:measurement_units,id',
        ]);

        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'measurement_unit_id' => $request->measurement_unit_id,
        ]);

        return to_route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        // Not part of initial requirements, but good to have for resource controllers
        $item->delete();
        return to_route('items.index')->with('success', 'Item deleted successfully.');
    }
}
