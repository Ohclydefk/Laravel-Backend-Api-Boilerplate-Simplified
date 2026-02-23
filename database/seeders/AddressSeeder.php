<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Str;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all user IDs
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Create 20 random addresses
        for ($i = 0; $i < 20; $i++) {
            Address::create([
                'user_id'     => $userIds[array_rand($userIds)],
                'label'       => fake()->randomElement(['Home', 'Office', 'Billing']),
                'street'      => fake()->streetAddress(),
                'barangay'    => fake()->streetName(),
                'city'        => fake()->city(),
                'province'    => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country'     => 'Philippines',
                'is_default'  => fake()->boolean(20),
            ]);
        }
    }
}
