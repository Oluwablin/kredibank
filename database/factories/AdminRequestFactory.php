<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\AdminRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = AdminRequest::class;
    public function definition()
    {
        return [
            'requester_id' => 1,
            'approver_id' => null,
            'user_id' => null,
            'request_type' => 'create',
            'payload' => null,
            'status' => 'pending',
        ];
    }
}
