<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    //

    public function register(Request $request)
    {
        //

        $validator = Validator::make($request->all(), [
            //'name' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:STUDENT,ORGANIZATOR,ADMINISTRATOR',
            'isActive' => 'required|boolean',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $user = User::create([
            'firstName' => $data['firstName'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'isActive' => $data['isActive'],
        ]);

    $token = $user->createToken('api_token')->plainTextToken;
    return response()->json([
        'message' => 'Registracija je uspesna',
        'user' => $user,
        'token' => $token
    ], 201);
      


    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }



        if(!Auth::attempt($validator->validated())){
            return response()->json([
                'message' => 'Neispravna email ili lozinka'
            ], 401);
        }





        $data = $validator->validated();
        $user = User::where('email', $data['email'])->first();

        if(!$user || !Hash::check($data['password'], $user->password)){
            return response()->json([
                'message' => 'Neispravna email ili lozinka'
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'message' => 'Uspesna prijava',
            'user' => $user,
            'token' => $token
        ], 200);    



    }















}
