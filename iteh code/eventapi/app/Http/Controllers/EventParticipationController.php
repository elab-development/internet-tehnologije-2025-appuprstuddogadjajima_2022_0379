<?php

namespace App\Http\Controllers;

use App\Models\EventParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventParticipationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return EventParticipation::all();
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
    'idEvent' => [
        'required',
        'integer',
        'exists:events,idEvent',
        // pravilo jedinstvene kombinacije sa idUser
        Rule::unique('event_participations')->where(function ($query) use ($request) {
            return $query->where('idUser', $request->idUser);
        }),
    ],
    'idUser' => 'required|integer|exists:users,id',
    'status' => 'required|string|in:REGISTERED,CANCELLED,ATTENDED',
    'registeredAt' => 'required|date',
    'cancelledAt' => 'nullable|date',
    'attendanceMarkedAt' => 'nullable|date',
]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $eventParticipation = EventParticipation::create($data);
        return response()->json([
            $eventParticipation
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        return EventParticipation::where('idParticipation', $id)->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventParticipation $eventParticipation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
            $eventParticipation = EventParticipation::where('idParticipation', $id)->firstOrFail();
        if (!$eventParticipation) {
            return response()->json([   
                'message' => 'Učestvovanje nije pronađeno'
            ], 404);    

        
    }
        $validator = Validator::make($request->all(), [
            'idEvent' => [
                'sometimes',
                'required',
                'integer',
                'exists:events,idEvent',
                // pravilo jedinstvene kombinacije sa idUser
                Rule::unique('event_participations')->where(function ($query) use ($request, $id) {
                    return $query->where('idUser', $request->idUser)
                                 ->where('idParticipation', '!=', $id);
                }),
            ],
            'idUser' => 'sometimes|required|integer|exists:users,id',
            'status' => 'sometimes|required|string|in:REGISTERED,CANCELLED,ATTENDED',
            'registeredAt' => 'sometimes|required|date',
            'cancelledAt' => 'nullable|date',
            'attendanceMarkedAt' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $eventParticipation->update($data);
        return response()->json([
            $eventParticipation
        ], 200);


    }

    
    public function destroy($id)
    {
        //
        $eventParticipation = EventParticipation::where('idParticipation', $id)->first();
        if(!$eventParticipation){
            return response()->json([
                'message' => 'Učestvovanje nije pronađeno'
            ], 404);
        }
        $eventParticipation->delete();
        return response()->json([
            'message' => 'Učestvovanje je obrisano'
        ], 200);
    }
}

