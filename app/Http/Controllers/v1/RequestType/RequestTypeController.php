<?php

namespace App\Http\Controllers\v1\RequestType;

use App\Models\RequestType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ApprovalRequest;
use App\Events\RequestApprovalEvent;
use App\Models\UserDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateUserDetailRequest;
use App\Http\Requests\UpdateUserDetailRequest;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\AdminRequest;
use Illuminate\Support\Facades\Auth;

class RequestTypeController extends Controller
{
    protected $admin;

    public function __construct() {
        $this->admin = Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * display all pending requests
     */
    public function dispalyAllPendingRequests()
    {
        $pending_requests = AdminRequest::where('status', 'pending');

        if ($pending_requests->exists()){
            return $this->successWithData($pending_requests->get());
        } else{
            return $this->success('No Pending requests');
        }
    }

    /**
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * approve a request
     */
    public function approveRequest(Request $request)
    {
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'request_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $admin_request = AdminRequest::where('id', $request->request_id);
        if ($admin_request->exists()) {
            $admin_request = $admin_request->first();
            if($admin_request->requester_id != Auth::id()){
                if ($admin_request->status == 'pending'){

                    $statement = $this->databaseQuery($admin_request->payload, $admin_request->request_type, $admin_request->user_id);
                    if ($statement === true){
                        $user_details = UserDetail::where('email', $admin_request->payload['email'])->first();
                        if ($user_details) {
                            $created_by = User::where('id', $admin_request->requester_id)->first();
                            $user_details->created_by = $created_by->firstname . ' ' . $created_by->lastname;
                            $user_details->is_approved = true;
                            $user_details->approved_by = Auth::user()->firstname . ' ' . Auth::user()->lastname;
                            $user_details->update();
                        }
                        $admin_request->status = 'approved';
                        $admin_request->approver_id = Auth::id();
                        if ($admin_request->update()){
                            return $this->success();
                        }
                    }else{
                        return $this->error('Something went wrong. Please try again');
                    }
                }else{
                    return $this->error('Request already '.$admin_request->status);
                }
            } else{
                return $this->error('You are not authorised to approve this request', 401);
            }
        }else{
            return $this->error('Request not found');
        }
    }

    /**
     * 
     *
     * @param  \App\RequestType  $requestType
     * @return \Illuminate\Http\Response
     * decline a request
     */
    public function declineRequest(Request $request)
    {
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'request_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $admin_request = AdminRequest::where('id', $request->request_id);
        if ($admin_request->exists()) {
            $admin_request = $admin_request->first();
            if($admin_request->requester_id != Auth::id()){
                if ($admin_request->status == 'pending'){
                    $admin_request->status = 'declined';
                    $admin_request->approver_id = Auth::id();
                    if ($admin_request->update()){
                        return $this->success('Decline successful');
                    } else{
                        return $this->error('Something went wrong. Please try again');
                    }
                }else{
                    return $this->error('Request already '.$admin_request->status);
                }
            } else{
                return $this->error('You are not authorised to approve this request', 401);
            }
        }else{
            return $this->error('Request not found');
        }
    }

    public function allUserDetails(){
        $users = UserDetail::all();
        if($users->count()>0){
            return $this->successWithData($users);
        } else{
            return $this->success('No users available');
        }
    }

    public function addUserRequest(Request $request){
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'firstname' => 'required|string|min:2|max:50',
            'lastname' => 'required|string|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $new_request = new AdminRequest();
        $new_request->requester_id = Auth::id();
        $new_request->request_type = 'create';
        //$new_request->request_type_id = 1;
        $new_request->payload = $credentials;
        $new_request->status = 'pending';

        if($new_request->save()){
            $this->sendMail(Auth::id());

            return $this->success('Request submitted successfully. Please wait for approval');
        } else{
            return $this->error();
        }
    }

    public function updateUserRequest(Request $request){
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'user_id' => 'required',
            'email' => 'email',
            'firstname' => 'string|min:2|max:50',
            'lastname' => 'string|min:2|max:50',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $user = UserDetail::where('id', $credentials['user_id']);

        if ($user->exists()) {

            $new_request = new AdminRequest();
            $new_request->requester_id = Auth::id();
            $new_request->user_id = $credentials['user_id'];
            $new_request->request_type = 'update';
            //$new_request->request_type_id = 2;
            $new_request->payload = $credentials;
            $new_request->status = 'pending';

            if ($new_request->save()) {
                $this->sendMail(Auth::id());
                return $this->success('Request submitted successfully. Please wait for approval');
            } else {
                return $this->error();
            }
        }else{
            return $this->error('User not found');
        }
    }

    public function deleteUserRequest(Request $request){
        $credentials = $request->all();
        $validator = Validator::make($credentials, [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        $user = UserDetail::where('id', $request->user_id);

        if ($user->exists()) {
            $new_request = new AdminRequest();
            $new_request->requester_id = Auth::id();
            $new_request->user_id = $request->user_id;
            $new_request->request_type = 'delete';
            //$new_request->request_type_id = 3;
            $new_request->status = 'pending';

            if($new_request->save()){
                $this->sendMail(Auth::id());
                return $this->success('Request submitted successfully. Please wait for approval');
            } else{
                return $this->error();
            }
        }else{
            return $this->error('User not found');
        }
    }

    public function databaseQuery($data, $request_type, $user_id){
        if ($request_type == 'create'){
            $user = new UserDetail();
            $user->email = $data['email'];
            $user->firstname = $data['firstname'];
            $user->lastname = $data['lastname'];

            if($user->save()){
                return true;
            } else{
                return false;
            }
        } elseif($request_type == 'update'){
            unset($data['user_id']);
            $user = UserDetail::where('id', $user_id)->update($data);
            if($user){
                return true;
            } else{
                return false;
            }
        }elseif($request_type == 'delete'){
            if(UserDetail::where('id', $user_id)->delete()){
                return true;
            } else{
                return false;
            }
        }
        return false;
    }
}
