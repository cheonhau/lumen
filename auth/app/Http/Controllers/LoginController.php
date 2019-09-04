<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Services\UserService;
use App\User;
 
class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $userService = new UserService;

            $validation = $userService->validationLogin($request);
            if($validation->fails()) {
                $res['status'] = false;
                $res['message'] = $validation->messages();

                return response($res, 200);
            }

            $res = $userService->login($request);

            return response($res, 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}