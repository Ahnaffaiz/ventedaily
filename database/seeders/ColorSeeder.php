<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    public function run()
    {
        $colors = [
            ['color' => 'Red', 'desc' => 'Bright and vibrant red color.'],
            ['color' => 'Blue', 'desc' => 'Calm and soothing blue color.'],
            ['color' => 'Green', 'desc' => 'Natural and fresh green color.'],
            ['color' => 'Yellow', 'desc' => 'Bright and cheerful yellow color.'],
            ['color' => 'Purple', 'desc' => 'Royal and elegant purple color.'],
            ['color' => 'Orange', 'desc' => 'Warm and energetic orange color.'],
            ['color' => 'Pink', 'desc' => 'Soft and delicate pink color.'],
            ['color' => 'Black', 'desc' => 'Bold and classic black color.'],
            ['color' => 'White', 'desc' => 'Pure and clean white color.'],
            ['color' => 'Gray', 'desc' => 'Neutral and balanced gray color.'],
            ['color' => 'Cyan', 'desc' => 'Refreshing and bright cyan color.'],
            ['color' => 'Magenta', 'desc' => 'Vivid and eye-catching magenta color.'],
            ['color' => 'Brown', 'desc' => 'Earthy and warm brown color.'],
            ['color' => 'Maroon', 'desc' => 'Rich and deep maroon color.'],
            ['color' => 'Beige', 'desc' => 'Subtle and soft beige color.'],
            ['color' => 'Olive', 'desc' => 'Muted and natural olive color.'],
            ['color' => 'Teal', 'desc' => 'Cool and unique teal color.'],
            ['color' => 'Navy', 'desc' => 'Dark and sophisticated navy color.'],
            ['color' => 'Gold', 'desc' => 'Shiny and luxurious gold color.'],
            ['color' => 'Silver', 'desc' => 'Sleek and modern silver color.'],
            // Add more colors as needed...
        ];

        // Loop to generate hundreds of variations for demonstration purposes.
        for ($i = 0; $i < 200; $i++) {
            Color::create([
                'name' => 'Color-' . ($i + 1),
                'desc' => 'Generated color description for Color-' . ($i + 1),
            ]);
        }

        // Insert colors into the database.
    }
}
