<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\User;

class UserService {
    // Register : validation register
    public static function ruleValidationRegister ($merge = []) {
        // email noiw nay khong duoc trung khop nua
        return array_merge ([
            'name'  =>  'required',
            'email'     =>  'required|email|unique:users,email',
            'password'  => 'required'
        ], $merge);
    }
    public static function validationRegister ($request) {
        $messages = [
            'name.required'     => 'Your name Is Required',
            'email.required'    => 'The Email Is Required',
            'email.email'       => 'The email is not valid',
            'email.unique'      => 'The email is already Exist',
            'password.required' => 'The Password is required'
        ];

        $validators = Validator::make($request->all(), self::ruleValidationRegister(), $messages);

        return $validators;
    }
    public function register (Request $request) {
        try {
            $hasher = app()->make('hash');
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $hasher->make($request->input('password'));
            
            $save = User::create([
                'name'=> $name,
                'email'=> $email,
                'password'=> $password,
                'api_token'=> ''
            ]);
            $res['status'] = true;
            $res['message'] = 'Registration success!';

            return $res;
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['status'] = false;
            $res['message'] = $ex->getMessage();

            return $res;
        }
    }

    public function get_user () {
        $user = User::all();
        if ($user) {
              $res['status'] = true;
              $res['message'] = $user;
 
              return $res;
        }else{
          $res['status'] = false;
          $res['message'] = 'Cannot find user!';
 
          return $res;
        }
    }

    // Login : validation register
    public static function ruleValidationLogin ($merge = []) {
        // email noiw nay khong duoc trung khop nua
        return array_merge ([
            'email'     =>  'required|email',
            'password'  => 'required'
        ], $merge);
    }
    public static function validationLogin ($request) {
        $messages = [
            'email.required'    => 'The Email Is Required',
            'email.email'       => 'The email is not valid',
            'password.required' => 'The Password is required'
        ];

        $validators = Validator::make($request->all(), self::ruleValidationLogin(), $messages);

        return $validators;
    }

    public function login(Request $request)
    {
        $res = [];
        $email    = $request->input('email');
        try {
            $login = User::where('email', $email)->first();
            if ($login) {
                if ($login->count() > 0) {
                    if (Hash::check($request->input('password'), $login->password)) {
                        try {
                            $api_token = sha1($login->id_user.time());
 
                            $create_token = User::where('id', $login->id_user)->update(['api_token' => $api_token]);
                            $res['status'] = true;
                            $res['message'] = 'Success login';
                            $res['data'] =  $login;
                            $res['api_token'] =  $api_token;

                        } catch (\Illuminate\Database\QueryException $ex) {
                            $res['status'] = false;
                            $res['message'] = $ex->getMessage();
                        }
                    } else {
                        $res['success'] = false;
                        $res['message'] = 'name / email / password not found';
                    }
                } else {
                    $res['success'] = false;
                    $res['message'] = 'name / email / password  not found';
                }
            } else {
                $res['success'] = false;
                $res['message'] = 'name / email / password not found';
            }

            return $res;
        } catch (\Illuminate\Database\QueryException $ex) {
            $res['success'] = false;
            $res['message'] = $ex->getMessage();

            return $res;
        }
    }
}