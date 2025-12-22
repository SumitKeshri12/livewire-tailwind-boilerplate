<?php

namespace Database\Seeders;

use App\Models\BrandDetail;
use Illuminate\Database\Seeder;

class BrandDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BrandDetail::factory()->count(5)->create();
    }
}
