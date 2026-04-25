<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['name', 'category_id', 'price', 'description', 'available'];

    protected $appends = ['category_name'];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    // Appended so the Vue side always has a display name with no extra logic
    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? 'Menu Item';
    }
}
