<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\JsonResponser\JSONResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, JSONResponse;

    public function sendMail($user_id){
        $users = User::where('id', '!=', $user_id)->get();
        foreach ($users as $user){
            SendEmailJob::dispatch($user->email);
        }
    }
}
