<?php

namespace Database\Factories;

use App\Models\BrandDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BrandDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            
'brand_id' => $this->faker->randomElement(\App\Models\Brand::pluck('id')->toArray()) ?? null,
'description' => $this->faker->sentence,
'status' => $this->faker->randomElement(['Y','N']),
'brand_image' => $this->faker->sentence,
        ];
    }
}
