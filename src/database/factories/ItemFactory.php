<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'seller_id' => User::factory(),
            'status' => 1,
            'title' => $this->faker->words(3, true),
            'brand_name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(100, 10000),
            'description' => $this->faker->sentence(),
            'condition' => $this->faker->numberBetween(1, 4),
        ];
    }

        public function sold(): self
    {
        return $this->state(fn () => ['status' => 2]);
    }
}
