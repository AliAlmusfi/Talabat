<?php

namespace Database\Seeders;

use App\Models\OTP;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OTPsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OTP::factory(15)->create();
    }
}
