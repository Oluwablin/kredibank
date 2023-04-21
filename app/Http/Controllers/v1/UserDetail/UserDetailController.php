<?php

namespace App\Http\Controllers\v1\UserDetail;

use App\Models\RequestType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mail;
use App\Models\User;
use App\Mail\ApprovalRequest;
use App\Events\RequestApprovalEvent;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUserDetailRequest;
use App\Http\Requests\UpdateUserDetailRequest;
use Illuminate\Support\Facades\Validator;

class UserDetailController extends Controller
{
    /**
     * Create new resource.
     *
     * @return \Illuminate\Http\Response
     * create user detail
     */
    public function createUserDetails(CreateUserDetailRequest $request)
    {
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        try {
            DB::beginTransaction();

            $user_details = UserDetail::create(
                [
                    "firstname" => $request->firstname,
                    "lastname" => $request->lastname,
                    "email" => $request->email,
                    "request_type_id" => $request->request_type_id,
                    "created_by" => auth()->user()->firstname . ' ' . auth()->user()->lastname,
                ]
            );

            if ($user_details) {
        
            DB::commit();

                $users = User::select('email')->get();
            foreach ($users as $user) {
                event(new RequestApprovalEvent($user));
            }
                return response()->json([
                    'success' => true,
                    'message' => 'User Details Creation request successfully, Please wait for approval',
                    'data' => $user_details,
                ], 201);
            }
            

            return response()->json([
                'success' => false,
                'message' => 'Error, User Details Creation request could not be created',
                'data' => null,
            ], 500);
        } catch (\Throwable $th) {

            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }

    }

    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * update user detail
     */
    public function updateUserDetails(Request $request)
    {
        //
    }

    /**
     * 
     *
     * @param  \App\Request  $request
     * @return \Illuminate\Http\Response
     * delete user detail
     */
    public function destroyUserDetails(Request $request)
    {
        //
    }
}
