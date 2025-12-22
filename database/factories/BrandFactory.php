<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            
'country_id' => $this->faker->randomElement(\App\Models\Country::pluck('id')->toArray()) ?? null,
'status' => $this->faker->unique()->randomElement(range('A', 'Z')),
'bob' => $this->faker->dateTime(),
'start_date' => $this->faker->date(),
'start_time' => $this->faker->time(),
'status' => $this->faker->unique()->randomElement(range('A', 'Z')),
        ];
    }
}
