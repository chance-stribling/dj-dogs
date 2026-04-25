<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => MenuCategory::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:menu_categories,name',
            'sort_order' => 'integer|min:0',
        ]);

        $category = MenuCategory::create($validated);

        return response()->json(['data' => $category], 201);
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:menu_categories,name,' . $menuCategory->id,
            'sort_order' => 'integer|min:0',
        ]);

        $menuCategory->update($validated);

        return response()->json(['data' => $menuCategory]);
    }

    public function destroy(MenuCategory $menuCategory)
    {
        // Null out category_id on any items in this category —
        // they'll fall back to the "Menu Item" default via the appended attribute.
        $menuCategory->menuItems()->update(['category_id' => null]);
        $menuCategory->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
