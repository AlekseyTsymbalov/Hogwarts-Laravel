<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $books = Section::query()->where('slug', 'books')->first();
        $wands = Section::query()->where('slug', 'wands')->first();

        if (!$books || !$wands) {
            return; // секции не засеяны
        }

        Product::query()->insert([
            [
                'name' => 'Advanced Potion-Making',
                'price' => 19.99,
                'description' => 'Classic textbook.',
                'image' => null,
                'section_id' => $books->id,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'name' => 'Hogwarts: A History',
                'price' => 29.99,
                'description' => 'Must-have.',
                'image' => null,
                'section_id' => $books->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Elder Wand Replica',
                'price' => 199.99,
                'description' => 'Legendary wand.',
                'image' => null,
                'section_id' => $wands->id,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'name' => 'Basic Wand',
                'price' => 9.99,
                'description' => 'Entry-level wand.',
                'image' => null,
                'section_id' => $wands->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}