<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create roles first
        $adminRole = Role::create([
            'name' => 'Admin',
            'status' => 'Y',
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'status' => 'Y',
        ]);

        // Create countries
        $countries = [];
        for ($i = 0; $i < 5; $i++) {
            $countries[] = Country::create([
                'name' => $faker->country(),
                'code' => $faker->countryCode(),
                'phone_code' => '+' . $faker->numberBetween(1, 999),
                'currency' => $faker->currencyCode(),
            ]);
        }

        // Create states for each country
        $states = [];
        foreach ($countries as $country) {
            for ($i = 0; $i < 3; $i++) {
                $states[] = State::create([
                    'name' => $faker->state(),
                    'country_id' => $country->id,
                ]);
            }
        }

        // Create cities for each state
        $cities = [];
        foreach ($states as $state) {
            for ($i = 0; $i < 2; $i++) {
                $cities[] = City::create([
                    'name' => $faker->city(),
                    'state_id' => $state->id,
                ]);
            }
        }

        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'email_verified_at' => now(),
            'role_id' => $adminRole->id,
            'dob' => $faker->date('Y-m-d', '1990-01-01'),
            'profile' => $faker->imageUrl(200, 200, 'people'),
            'country_id' => $countries[0]->id,
            'state_id' => $states[0]->id,
            'city_id' => $cities[0]->id,
            'gender' => $faker->randomElement(['M', 'F']),
            'status' => 'Y',
            'description' => 'System Administrator',
            'skills' => json_encode(['php', 'laravel', 'javascript', 'vue']),
            'bg_color' => $faker->hexColor(),
            'timezone' => $faker->timezone(),
            'event_date' => $faker->date(),
            'event_datetime' => $faker->dateTime(),
            'event_time' => $faker->time('H:i:s'),
            'document' => $faker->fileExtension(),
            'age' => $faker->numberBetween(25, 65),
        ]);

        // Create 50 fake users
        for ($i = 0; $i < 50; $i++) {
            $randomCountry = $faker->randomElement($countries);
            $countryStates = collect($states)->where('country_id', $randomCountry->id);
            $randomState = $faker->randomElement($countryStates);
            $stateCities = collect($cities)->where('state_id', $randomState->id);
            $randomCity = $faker->randomElement($stateCities);

            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'role_id' => $faker->randomElement([$adminRole->id, $userRole->id]),
                'dob' => $faker->date('Y-m-d', '1990-01-01'),
                'profile' => $faker->optional(0.7)->imageUrl(200, 200, 'people'),
                'country_id' => $randomCountry->id,
                'state_id' => $randomState->id,
                'city_id' => $randomCity->id,
                'gender' => $faker->randomElement(['M', 'F']),
                'status' => $faker->randomElement(['Y', 'N']),
                'description' => $faker->optional(0.6)->sentence(),
                'skills' => $faker->optional(0.7)->randomElements(['php', 'laravel', 'javascript', 'vue', 'react', 'python', 'mysql'], $faker->numberBetween(1, 4)) ? json_encode($faker->randomElements(['php', 'laravel', 'javascript', 'vue', 'react', 'python', 'mysql'], $faker->numberBetween(1, 4))) : null,
                'bg_color' => $faker->optional(0.3)->hexColor(),
                'timezone' => $faker->optional(0.8)->timezone(),
                'event_date' => $faker->optional(0.4)->date(),
                'event_datetime' => $faker->optional(0.3)->dateTime(),
                'event_time' => $faker->optional(0.2)->time('H:i:s'),
                'document' => $faker->optional(0.1)->fileExtension(),
                'age' => $faker->optional(0.9)->numberBetween(18, 80),
            ]);
        }
    }
}
