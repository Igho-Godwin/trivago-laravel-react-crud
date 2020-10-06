<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'zip_code' => $this->faker->numberBetween(10000, 90000),
            'state' => substr($this->faker->state, 0, 255),
            'city' => substr($this->faker->city, 0, 255),
            'country' => substr($this->faker->country, 0, 255),
            'address' => substr($this->faker->address, 0, 255),

        ];
    }
}
