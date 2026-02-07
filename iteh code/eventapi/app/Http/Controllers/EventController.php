<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Event::all();
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startAt' => 'required|date',
            'endAt' => 'required|date|after_or_equal:startAt',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:ACTIVE,CANCELLED,DRAFT',
            'idCategory' => 'required|integer|exists:categories,idCategory',
            'idUser' => 'required|integer|exists:users,id',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }
        $data= $validator->validated();
        $event = Event::create($data);
        return response()->json([
            $event
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        return Event::where('idEvent', $id)->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $event = Event::where('idEvent', $id)->firstOrFail();
        if(!$event){
            return response()->json([
                'message' => 'Događaj nije pronađen'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'startAt' => 'sometimes|date',
            'endAt' => 'sometimes|date|after_or_equal:startAt',
            'location' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:ACTIVE,CANCELLED,DRAFT',
            'idCategory' => 'sometimes|integer|exists:categories,idCategory',
            'idUser' => 'sometimes|integer|exists:users,id',
        ]);
        if($validator->fails()){
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }
        $data = $validator->validated();
        $event->update($data);
        $event->refresh();
        return response()->json([
            $event
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $event = Event::where('idEvent', $id)->first();
        if(!$event){
            return response()->json([
                'message' => 'Događaj nije pronađen'
            ], 404);
        }
        $event->delete();
        return response()->json([
            'message' => 'Događaj je obrisan'
        ], 200);
    }
}
