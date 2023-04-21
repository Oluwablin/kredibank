<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequestType;

class RequestTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $request_types = [
            [
                'id'          => 1,
                'name'         => "Create",
            ],
            [
                'id'          => 2,
                'name'         => "Update",
            ],
            [
                'id'          => 3,
                'name'         => "Delete",
            ],
        ];

        foreach ($request_types as $key => $value) {
            $check_request_type = RequestType::where('name', $value['name'])->first();
            if(!$check_request_type){
                RequestType::create($value);
            }
        }
    }
}
