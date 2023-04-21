<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\UserDetail;
use App\Models\AdminRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use JWTAuth;

class GloverChallengeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUserWithAuthentication()
    {
        $data = [
            'email' => "ben_dobile@example.com",
            'firstname' => "Ben",
            'lastname' => 'Dobile',
        ];

        $response = $this->json('POST', '/api/v1/request/create', $data);
        $response->assertStatus(401);
    }
    public function testUpdateUserWithAuthentication()
    {

        $user = UserDetail::factory()->create();
        $data = [
            'lastname' => 'Gabriel',
            'user_id' => $user->id,
        ];
        $response = $this->json('PATCH', '/api/v1/request/update', $data);
        $response->assertStatus(401);
    }
    public function testDeleteUserWithAuthentication()
    {

        $user = UserDetail::factory()->create();
        $data = [
            'user_id' => $user->id,
        ];
        $response = $this->json('DELETE', '/api/v1/request/delete', $data);
        $response->assertStatus(401);
    }
    public function testRequestThatArePendingList()
    {
        $admin = User::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $user = UserDetail::factory()->create();
        $data = [
            'firstname' => "Ben",
            'user_id'=> $user->id
        ];
        $admin_request = AdminRequest::factory()->count(4)->state(new Sequence(
            ['request_type'=>'update'],
            ['request_type'=>'delete']
        ))->create(
            [
                'payload'=>$data,
                'user_id'=>$user->id,
                'requester_id'=>$admin->id
            ]
        );
        $response = $this->json('GET', '/api/v1/request/fetch/all', [], ['Authorization' => "Bearer $token"]);
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'success',
                'message',
                'data'=> [
                    '*' => [
                        'id',
                        'user_id',
                        'request_type',
                        'payload',
                        'status',
                    ]
                ]
            ]
        );
    }
    public function testApproveARequestWithAuthentication()
    {

        $user = UserDetail::factory()->create();
        $data = [
            'lastname' => 'Gabriel',
            'user_id' => $user->id,
        ];
        $response = $this->json('PATCH', '/api/v1/request/update', $data);
        $response->assertStatus(401);
    }
    public function testApproveARequestIfItsTheSameAdmin()
    {

        $user = UserDetail::factory()->create();
        $admin = User::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $data = [
            'firstname' => "Ben",
            'user_id' => $user->id,
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'requester_id'=>$admin->id

        ]);

        $response = $this->json('PUT', '/api/v1/request/approve',['request_id' => $admin_request->id],['Authorization' => "Bearer $token"]);
        $response->assertStatus(401);
    }
    public function testApproveARequestIfItsAnotherAdmin()
    {

        $user = UserDetail::factory()->create();
        $admin = User::factory()->create();
        $data = [
            'firstname' => "Ben",
            'user_id' => $user->id,
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'requester_id'=>$admin->id

        ]);

        //Different Admin
        $admin2 = User::factory()->create();
        $token2 = JWTAuth::fromUser($admin2);

        $response2 = $this->json('PUT', '/api/v1/request/approve',['request_id' => $admin_request->id],['Authorization' => "Bearer $token2"]);
        $response2->assertStatus(200);
    }

    public function testDeclineARequestIfItsTheSameAdmin()
    {
        $user = UserDetail::factory()->create();
        $admin = User::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $data = [
            'firstname' => "Ben",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'requester_id'=>$admin->id

        ]);

        $response = $this->json('PUT', '/api/v1/request/decline',['request_id' => $admin_request->id],['Authorization' => "Bearer $token"]);
        $response->assertStatus(401);
    }
    public function testDeclineARequestIfItsAnotherAdmin()
    {

        $user = UserDetail::factory()->create();
        $admin = User::factory()->create();
        $data = [
            'firstname' => "Ben",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'requester_id'=>$admin->id

        ]);

        //Different Admin
        $admin2 = User::factory()->create();
        $token2 = JWTAuth::fromUser($admin2);

        $response2 = $this->json('PUT', '/api/v1/request/decline',['request_id' => $admin_request->id],['Authorization' => "Bearer $token2"]);
        $response2->assertStatus(200);
    }
}
