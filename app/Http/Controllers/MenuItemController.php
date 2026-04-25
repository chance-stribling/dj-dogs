<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => MenuItem::with('category')
                ->orderByRaw('ISNULL(category_id), category_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function count()
    {
        return response()->json(['total' => MenuItem::count()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:menu_categories,id',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'available'   => 'boolean',
        ]);

        return response()->json(['data' => MenuItem::create($validated)], 201);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'nullable|exists:menu_categories,id',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'available'   => 'boolean',
        ]);

        $menuItem->update($validated);

        return response()->json(['data' => $menuItem->fresh('category')]);
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function toggle(MenuItem $menuItem)
    {
        $menuItem->update(['available' => !$menuItem->available]);

        return response()->json(['data' => $menuItem]);
    }
}
