<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insertData = [
                [
                'id' => 1,
                'category_name' => Category::CATEGORY_NAMES[0]
                ],
                [
                'id' => 2,
                'category_name' => Category::CATEGORY_NAMES[1]
                ],
                [
                'id' => 3,
                'category_name' => Category::CATEGORY_NAMES[2]
                ],
                [
                'id' => 4,
                'category_name' => Category::CATEGORY_NAMES[0]
                ],
                [
                'id' => 5,
                'category_name' => Category::CATEGORY_NAMES[1]
                ],
                [
                'id' => 6,
                'category_name' => Category::CATEGORY_NAMES[2]
                ]
            ];

        Category::insert($insertData);
    }
}