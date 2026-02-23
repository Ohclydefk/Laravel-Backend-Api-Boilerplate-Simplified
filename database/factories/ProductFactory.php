<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraph(),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 200),
            'is_active' => $this->faker->boolean(90), // 90% chance active
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
        ];
    }
}
