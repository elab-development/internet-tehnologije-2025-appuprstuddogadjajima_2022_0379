<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Notification::all();
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
            'idUser' => 'required|integer|exists:users,id',
            'idEvent' => 'required|integer|exists:events,idEvent',
            'message' => 'required|string',
            'type' => 'required|string|in:REMINDER,UPDATE,CANCELLATION',
            'seen' => 'required|boolean',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $notification = Notification::create($data);
        return response()->json([
            $notification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        return Notification::where('idNotification', $id)->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $notification = Notification::where('idNotification', $id)->firstOrFail();
        if(!$notification){
            return response()->json([
                'message' => 'Notifikacija nije pronađena'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'idUser' => 'sometimes|required|integer|exists:users,id',
            'idEvent' => 'sometimes|required|integer|exists:events,idEvent',
            'message' => 'sometimes|required|string',
            'type' => 'sometimes|required|string|in:REMINDER,UPDATE,CANCELLATION',
            'seen' => 'sometimes|required|boolean',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $notification->update($data);
        return response()->json([
            $notification
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
     {
         //
         $notification = Notification::where('idNotification', $id)->first();
         if(!$notification){
             return response()->json([
                 'message' => 'Notifikacija nije pronađena'
             ], 404);
         }
         $notification->delete();
         return response()->json([
             'message' => 'Notifikacija je obrisana'
         ], 200);
     }
  
}
