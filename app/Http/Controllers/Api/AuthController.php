<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:'.User::class],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ],
        [],
        [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
             ]);
             if ($validator->fails()) {
                return ApiResponse::sendResponse(422,'Validation Error', $validator->messages()->all());
            }
            
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)    
        ]);
        $data['token'] = $user->createToken('authToken')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;

        return ApiResponse::sendResponse(201, 'User created successfully', $data); 

    }
}
