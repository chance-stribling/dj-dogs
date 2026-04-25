<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the categories table
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 2. Pull every distinct category string out of menu_items and insert as rows
        $existing = DB::table('menu_items')
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        foreach ($existing as $index => $name) {
            DB::table('menu_categories')->insert([
                'name'       => $name,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Add the category_id FK column to menu_items (nullable during transition)
        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('name')
                ->constrained('menu_categories')
                ->nullOnDelete();
        });

        // 4. Populate category_id by matching the old string value to the new table
        DB::statement('
            UPDATE menu_items mi
            JOIN menu_categories mc ON mc.name = mi.category
            SET mi.category_id = mc.id
        ');

        // 5. Drop the old string column
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        // Re-add the string column
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('category')->after('name')->default('');
        });

        // Restore the string values from the category relation
        DB::statement('
            UPDATE menu_items mi
            JOIN menu_categories mc ON mc.id = mi.category_id
            SET mi.category = mc.name
        ');

        // Drop the FK column
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('menu_categories');
    }
};
