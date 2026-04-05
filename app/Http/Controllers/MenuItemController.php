<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::where('available', true)
            ->orderByRaw("FIELD(category, 'Signature Dogs', 'Classics', 'Sides')")
            ->orderBy('name')
            ->get();

        return Inertia::render('Welcome', [
            'menuItems' => $menuItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'available'   => 'boolean',
        ]);

        return MenuItem::create($validated);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'category'    => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'available'   => 'sometimes|boolean',
        ]);

        $menuItem->update($validated);

        return $menuItem;
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return response()->noContent();
    }
}
