<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return User::all();

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
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
        $user = User::create($data);
        return response()->json([
            $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        return User::where('id', $id)->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::where('id',$id)->firstOrFail();
        if(!$user){
            return response()->json([
                'message' => 'Korisnik nije pronađen'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'firstName' => 'sometimes|required|string|max:255',
            'lastName' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string|in:STUDENT,ORGANIZATOR,ADMINISTRATOR',
            'isActive' => 'sometimes|required|boolean',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();
        $user->update($data);
        $user->refresh();
            return response()->json([
                $user
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        if(!$user){
            return response()->json([
                'message' => 'Korisnik nije pronađen'
            ], 404);
        }
        $user->delete();
        return response()->json([
            'message' => 'Korisnik je obrisan'
        ], 200);
    }
}
