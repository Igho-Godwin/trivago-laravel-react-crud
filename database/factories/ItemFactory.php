<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $arr = [
            'name' => substr($this->faker->company, 0, 10),
            'rating' => $this->faker->numberBetween(0, 5),
            'category' => 'hotel',
            'image' => substr($this->faker->url, 0, 255),
            'reputation' => $this->faker->numberBetween(0, 700),
            'price' => $this->faker->numberBetween(0, 5000),
            'availability' => $this->faker->numberBetween(0, 700),
            'reputation_badge' => 'red',
        ];

        return $arr;
    }
}
