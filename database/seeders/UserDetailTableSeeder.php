<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDetail;

class UserDetailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_details = [
            [
                'firstname'          => "Random",
                'lastname'           => "User",
                'email'               => "random_user@example.com",
                'created_by'          =>  "Another Administrator",
                'approved_by'         =>  "Default Admin",
            ],
            [
                'firstname'          => "John",
                'lastname'           => "Doe",
                'email'               => "john_doe@example.com",
                'created_by'          =>  "Another Administrator",
                'approved_by'         =>  "Default Admin",
            ],
        ];

        foreach ($user_details as $key => $value) {
            $check_user_detail = UserDetail::where('email', $value['email'])->first();
            if (!$check_user_detail) {
                UserDetail::create($value);
            }
        }
    }
}
