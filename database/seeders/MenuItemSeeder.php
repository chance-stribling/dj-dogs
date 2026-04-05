<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Signature Dogs
            [
                'name'        => 'The Big Dog',
                'category'    => 'Signature Dogs',
                'description' => 'Our signature all-beef frank loaded with chili, cheddar, diced onions, and jalapeños. The one that started it all.',
                'price'       => 8.99,
                'available'   => true,
            ],
            [
                'name'        => 'The Mama Dog',
                'category'    => 'Signature Dogs',
                'description' => 'A classic dog topped with homemade relish, sweet peppers, and a secret sauce only Mama knows.',
                'price'       => 7.99,
                'available'   => true,
            ],
            [
                'name'        => 'The Florida Man',
                'category'    => 'Signature Dogs',
                'description' => 'Grilled dog with mango salsa, crispy bacon bits, and a drizzle of sriracha aioli.',
                'price'       => 9.49,
                'available'   => true,
            ],
            [
                'name'        => 'The Smokehouse',
                'category'    => 'Signature Dogs',
                'description' => 'Slow-smoked sausage link topped with pulled pork, pickled red onions, and smoky BBQ sauce.',
                'price'       => 10.49,
                'available'   => true,
            ],
            [
                'name'        => 'The Gulf Coast',
                'category'    => 'Signature Dogs',
                'description' => 'All-beef frank with shrimp remoulade, shredded lettuce, and a squeeze of fresh lemon.',
                'price'       => 11.99,
                'available'   => true,
            ],

            // Classics
            [
                'name'        => 'Classic Dog',
                'category'    => 'Classics',
                'description' => 'All-beef frank with your choice of ketchup, mustard, or both. Simple. Perfect.',
                'price'       => 4.99,
                'available'   => true,
            ],
            [
                'name'        => 'Chili Cheese Dog',
                'category'    => 'Classics',
                'description' => 'Smothered in homemade beef chili and melted cheddar cheese.',
                'price'       => 6.99,
                'available'   => true,
            ],
            [
                'name'        => 'Slaw Dog',
                'category'    => 'Classics',
                'description' => 'Topped with creamy coleslaw, mustard, and a sprinkle of celery salt.',
                'price'       => 5.99,
                'available'   => true,
            ],
            [
                'name'        => 'Corn Dog',
                'category'    => 'Classics',
                'description' => 'All-beef frank hand-dipped in sweet cornbread batter and fried golden.',
                'price'       => 4.49,
                'available'   => true,
            ],
            [
                'name'        => 'Chicago Style',
                'category'    => 'Classics',
                'description' => 'Yellow mustard, relish, onion, tomato, sport peppers, pickle, and celery salt. No ketchup. Ever.',
                'price'       => 6.49,
                'available'   => true,
            ],

            // Sides
            [
                'name'        => 'Loaded Fries',
                'category'    => 'Sides',
                'description' => 'Crispy fries topped with chili, cheese, and jalapeños.',
                'price'       => 5.49,
                'available'   => true,
            ],
            [
                'name'        => 'Onion Rings',
                'category'    => 'Sides',
                'description' => 'Beer-battered and fried golden brown.',
                'price'       => 4.49,
                'available'   => true,
            ],
            [
                'name'        => 'Mac & Cheese Bites',
                'category'    => 'Sides',
                'description' => 'Creamy mac and cheese breaded and fried into crispy little bites.',
                'price'       => 4.99,
                'available'   => true,
            ],
            [
                'name'        => 'Coleslaw',
                'category'    => 'Sides',
                'description' => 'Homemade creamy coleslaw. A classic pairing with anything on the menu.',
                'price'       => 2.99,
                'available'   => true,
            ],
            [
                'name'        => 'Chips & Queso',
                'category'    => 'Sides',
                'description' => 'Crispy tortilla chips served with a warm house-made queso dip.',
                'price'       => 3.99,
                'available'   => true,
            ],
        ];

        MenuItem::insert($items);
    }
}
