<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Section::query()->insert([
            [
                'name' => 'Books',
                'slug' => 'books',
                'sort' => 10,
                'active' => true,
                'description' => 'All magic books',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wands',
                'slug' => 'wands',
                'sort' => 20,
                'active' => true,
                'description' => 'Magic wands',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Potions',
                'slug' => 'potions',
                'sort' => 30,
                'active' => false,
                'description' => 'Potions and ingredients',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
