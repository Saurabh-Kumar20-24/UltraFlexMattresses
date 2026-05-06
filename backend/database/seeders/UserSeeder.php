<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'email'=>'admin@ultraflexmattresses.com'
        ]);

        User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' =>'superadmin@ultraflexmattresses.com'
        ]);

        User::factory()->customer()->create([
            'name'=>'Test customer',
            'email'=>'customer@test.com'
        ]);

        User::factory()->customer()->count(10)->create();
    }
}
