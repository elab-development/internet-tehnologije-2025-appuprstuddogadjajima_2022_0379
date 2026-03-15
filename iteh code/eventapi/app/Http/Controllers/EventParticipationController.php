<?php

namespace App\Http\Controllers;

use App\Models\EventParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class EventParticipationController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="EventParticipation",
     *     type="object",
     *     @OA\Property(property="idParticipation", type="integer", example=1),
     *     @OA\Property(property="idUser", type="integer", example=3),
     *     @OA\Property(property="idEvent", type="integer", example=5),
     *     @OA\Property(property="status", type="string", example="REGISTERED"),
     *     @OA\Property(property="registeredAt", type="string", format="date-time", example="2026-04-10 12:00:00"),
     *     @OA\Property(property="cancelledAt", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="attendanceMarkedAt", type="string", format="date-time", nullable=true),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */

    private function canAccess(EventParticipation $p): bool
    {
        $role = strtoupper(trim(auth()->user()->role));
        return in_array($role, ['ORGANIZATOR', 'ADMIN'], true) || $p->idUser === auth()->id();
    }

    /**
     * @OA\Get(
     *     path="/api/event-participations",
     *     summary="Prikaz učestvovanja na događajima",
     *     description="ADMIN/ORGANIZATOR vide sva učestvovanja, ostali vide samo svoja.",
     *     tags={"Event Participations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćena lista učestvovanja",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/EventParticipation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     )
     * )
     */
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
     *
     * @OA\Post(
     *     path="/api/event-participations",
     *     summary="Prijava korisnika na događaj",
     *     tags={"Event Participations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idEvent","status","registeredAt"},
     *             @OA\Property(property="idEvent", type="integer", example=5),
     *             @OA\Property(property="status", type="string", example="REGISTERED"),
     *             @OA\Property(property="registeredAt", type="string", format="date-time", example="2026-04-10 12:00:00"),
     *             @OA\Property(property="cancelledAt", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="attendanceMarkedAt", type="string", format="date-time", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Učestvovanje uspešno kreirano",
     *         @OA\JsonContent(ref="#/components/schemas/EventParticipation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije ili korisnik već prijavljen na događaj"
     *     )
     * )
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
     *
     * @OA\Get(
     *     path="/api/event-participations/{id}",
     *     summary="Prikaz jednog učestvovanja na događaju",
     *     tags={"Event Participations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID učestvovanja (idParticipation)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćeno učestvovanje",
     *         @OA\JsonContent(ref="#/components/schemas/EventParticipation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Zabranjen pristup"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Učestvovanje nije pronađeno"
     *     )
     * )
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
     *
     * @OA\Put(
     *     path="/api/event-participations/{id}",
     *     summary="Izmena učestvovanja na događaju",
     *     tags={"Event Participations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID učestvovanja (idParticipation)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="idEvent", type="integer", example=5),
     *             @OA\Property(property="status", type="string", example="CANCELLED"),
     *             @OA\Property(property="registeredAt", type="string", format="date-time", example="2026-04-10 12:00:00"),
     *             @OA\Property(property="cancelledAt", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="attendanceMarkedAt", type="string", format="date-time", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Učestvovanje uspešno izmenjeno",
     *         @OA\JsonContent(ref="#/components/schemas/EventParticipation")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Zabranjen pristup"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Učestvovanje nije pronađeno"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
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


    /**
     * @OA\Delete(
     *     path="/api/event-participations/{id}",
     *     summary="Brisanje učestvovanja na događaju",
     *     tags={"Event Participations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID učestvovanja (idParticipation)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Učestvovanje uspešno obrisano"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Zabranjen pristup"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Učestvovanje nije pronađeno"
     *     )
     * )
     */
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

