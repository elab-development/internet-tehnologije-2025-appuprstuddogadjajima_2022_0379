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

    private function canAccess(EventParticipation $p): bool
{
    $role = strtoupper(trim(auth()->user()->role));
    return in_array($role, ['ORGANIZATOR', 'ADMIN'], true) || $p->idUser === auth()->id();
}
    public function index()
{
    $user = auth()->user();
    $role = strtoupper(trim($user->role));

    $q = EventParticipation::query();

    if (!in_array($role, ['ORGANIZATOR', 'ADMIN'], true)) {
        $q->where('idUser', $user->id);
    }

    return response()->json($q->get());
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
    $userId = auth()->id();

    $validator = Validator::make($request->all(), [
        'idEvent' => [
            'required',
            'integer',
            'exists:events,idEvent',
            Rule::unique('event_participations')->where(fn ($q) => $q->where('idUser', $userId)),
        ],
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
    $data['idUser'] = $userId;

    $eventParticipation = EventParticipation::create($data);

    return response()->json($eventParticipation, 201);
}

    /**
     * Display the specified resource.
     */
   public function show($id)
{
    $p = EventParticipation::where('idParticipation', $id)->firstOrFail();

    if (!$this->canAccess($p)) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    return response()->json($p);
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
    $p = EventParticipation::where('idParticipation', $id)->firstOrFail();

    if (!$this->canAccess($p)) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    // ne dozvoli menjanje idUser kroz update
    $validator = Validator::make($request->all(), [
        'idEvent' => [
            'sometimes','required','integer','exists:events,idEvent',
            Rule::unique('event_participations')->where(fn ($q) => $q
                ->where('idUser', $p->idUser)
                ->where('idParticipation', '!=', $id)
            ),
        ],
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

    $p->update($validator->validated());

    return response()->json($p, 200);
}


    
    public function destroy($id)
{
    $p = EventParticipation::where('idParticipation', $id)->firstOrFail();

    if (!$this->canAccess($p)) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $p->delete();

    return response()->json(['message' => 'Učestvovanje je obrisano'], 200);
}
}

