<?php

namespace App\Http\Controllers\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Login a User
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email or password',
                'data' => null
            ]);
        }
        // Data to return
        $data = [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'You are logged in successfully',
            'data' => $data
        ]);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
            'data' => null
        ]);
    }

    //Register a User or Admin
    public function register(Request $request)
    {
        //Validate data
        $data = $request->only('firstname', 'lastname', 'email', 'password');
        $validator = Validator::make($data, [
            'firstname' => 'required|string|min:2|max:50',
            'lastname' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return $this->errorValidation($validator->errors());
        }

        //Request is valid, create new user
        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        //User created, return success response
        return $this->successWithData($user,'Admin created successfully');
    }
}
