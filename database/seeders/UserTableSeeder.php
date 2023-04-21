<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'firstname' => 'Default',
                'lastname' => 'Admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10),
            ],
            [
                'firstname' => 'Another',
                'lastname' => 'Administrator',
                'email' => 'another_administrator@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'remember_token' => Str::random(10)
            ]
        ];

        foreach ($users as $key => $value) {
            $check_user = User::where('email', $value['email'])->first();
            if (!$check_user) {
                $user = User::create($value);

                User::factory()->count(2)->create();
            }
        }
    }
}
