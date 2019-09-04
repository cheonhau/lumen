<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    public function register(Request $request) {
        try {
            $userService = new UserService;

            $validation = $userService->validationRegister($request);
            if($validation->fails()) {
                $res['status'] = false;
                $res['message'] = $validation->messages();

                return response($res, 200);
            }

            $res = $userService->register($request);

            return response($res, 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
 
    public function get_user() {
        try {
            $res = (new UserService)->get_user();
            return response($res);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}