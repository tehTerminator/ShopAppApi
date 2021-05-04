<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'displayName' => 'Administrator',
            'username' => 'administrator',
            'password' => Hash::make('myPassword')
        ]);

        User::create([
            'displayName' => 'Prateek Kher',
            'username' => 'prateekkher',
            'password' => Hash::make('demo112')
        ]);
    }
}
