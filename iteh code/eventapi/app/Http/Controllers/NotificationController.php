<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class NotificationController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Notification",
     *     type="object",
     *     @OA\Property(property="idNotification", type="integer", example=1),
     *     @OA\Property(property="idUser", type="integer", example=1),
     *     @OA\Property(property="idEvent", type="integer", example=5),
     *     @OA\Property(property="message", type="string", example="Podsetnik: događaj počinje za 1 sat"),
     *     @OA\Property(property="type", type="string", example="REMINDER"),
     *     @OA\Property(property="seen", type="boolean", example=false),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */

    private function assertOwner(Notification $notification)
{
    if ($notification->idUser !== auth()->id()) {
        abort(response()->json(['message' => 'Forbidden'], 403));
    }
}
    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     summary="Prikaz svih notifikacija ulogovanog korisnika",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćena lista notifikacija",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Notification")
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
        $notifications = Notification::where('idUser', auth()->id())->get();
        return response()->json($notifications);
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
     *     path="/api/notifications",
     *     summary="Kreiranje nove notifikacije",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"idEvent","message","type"},
     *             @OA\Property(property="idEvent", type="integer", example=5),
     *             @OA\Property(property="message", type="string", example="Podsetnik: događaj počinje sutra u 18h"),
     *             @OA\Property(property="type", type="string", example="REMINDER"),
     *             @OA\Property(property="seen", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notifikacija uspešno kreirana",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Neautorizovan pristup"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
     */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'idEvent' => 'required|integer|exists:events,idEvent',
        'message' => 'required|string',
        'type' => 'required|string|in:REMINDER,UPDATE,CANCELLATION',
        'seen' => 'sometimes|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validacija nije prošla',
            'errors' => $validator->errors()
        ], 422);
    }

    $data = $validator->validated();
    $data['idUser'] = auth()->id();
    $data['seen'] = $data['seen'] ?? false;

    $notification = Notification::create($data);

    return response()->json($notification, 201);
}

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/notifications/{id}",
     *     summary="Prikaz jedne notifikacije",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID notifikacije (idNotification)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćena notifikacija",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
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
     *         description="Notifikacija nije pronađena"
     *     )
     * )
     */
   public function show($id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();
    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }
    return response()->json($notification);
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
     *
     * @OA\Put(
     *     path="/api/notifications/{id}",
     *     summary="Izmena postojeće notifikacije",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID notifikacije (idNotification)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="idEvent", type="integer", example=5),
     *             @OA\Property(property="message", type="string", example="Ažurirana poruka notifikacije"),
     *             @OA\Property(property="type", type="string", example="UPDATE"),
     *             @OA\Property(property="seen", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifikacija uspešno izmenjena",
     *         @OA\JsonContent(ref="#/components/schemas/Notification")
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
     *         description="Notifikacija nije pronađena"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
     */
   public function update(Request $request, $id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();

    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $validator = Validator::make($request->all(), [
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

    $notification->update($validator->validated());

    return response()->json($notification, 200);
}

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     summary="Brisanje notifikacije",
     *     tags={"Notifications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID notifikacije (idNotification)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notifikacija uspešno obrisana"
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
     *         description="Notifikacija nije pronađena"
     *     )
     * )
     */
    public function destroy($id)
{
    $notification = Notification::where('idNotification', $id)->firstOrFail();

    if ($notification->idUser !== auth()->id()) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    $notification->delete();

    return response()->json(['message' => 'Notifikacija je obrisana'], 200);
}
  
}
