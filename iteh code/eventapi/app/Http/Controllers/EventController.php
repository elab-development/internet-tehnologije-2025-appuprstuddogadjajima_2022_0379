<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\Notification;
use App\NotificationType;
use App\ParticipationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Event",
     *     type="object",
     *
     *     @OA\Property(property="idEvent", type="integer", example=1),
     *     @OA\Property(property="idUser", type="integer", example=1),
     *     @OA\Property(property="idCategory", type="integer", example=2),
     *     @OA\Property(property="title", type="string", example="IT Konferencija 2026"),
     *     @OA\Property(property="description", type="string", example="Opis događaja"),
     *     @OA\Property(property="location", type="string", example="Beograd"),
     *     @OA\Property(property="startAt", type="string", format="date-time", example="2026-04-15T18:00:00"),
     *     @OA\Property(property="endAt", type="string", format="date-time", example="2026-04-15T21:00:00"),
     *     @OA\Property(property="capacity", type="integer", example=150),
     *     @OA\Property(property="status", type="string", example="AKTIVAN"),
     *     @OA\Property(property="created_at", type="string", format="date-time"),
     *     @OA\Property(property="updated_at", type="string", format="date-time")
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Prikaz svih događaja",
     *     tags={"Events"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćena lista događaja"
     *     )
     * )
     */
    public function index()
    {
        return Event::query()
            ->with(['category:idCategory,name'])
            ->withCount([
                'eventParticipations as registeredCount' => function ($q) {
                    $q->where('status', 'REGISTERED');
                },
            ])
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/events",
     *     summary="Kreiranje novog događaja",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"idUser","idCategory","title","description","location","startAt","endAt","capacity","status"},
     *
     *             @OA\Property(property="idUser", type="integer", example=1),
     *             @OA\Property(property="idCategory", type="integer", example=2),
     *             @OA\Property(property="title", type="string", example="IT Konferencija 2026"),
     *             @OA\Property(property="description", type="string", example="Velika studentska konferencija iz oblasti informacionih tehnologija."),
     *             @OA\Property(property="location", type="string", example="Beograd"),
     *             @OA\Property(property="startAt", type="string", format="date-time", example="2026-04-15 18:00:00"),
     *             @OA\Property(property="endAt", type="string", format="date-time", example="2026-04-15 21:00:00"),
     *             @OA\Property(property="capacity", type="integer", example=150),
     *             @OA\Property(property="status", type="string", example="AKTIVAN")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Događaj uspešno kreiran"
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
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
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
            'status' => 'required|in:ACTIVE,CANCELLED,DRAFT,FINISHED',
            'idCategory' => 'required|integer|exists:categories,idCategory',
            'idUser' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();
        $event = Event::create($data);

        return response()->json([
            $event,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Prikaz jednog događaja",
     *     tags={"Events"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID događaja (idEvent)",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Uspešno vraćen događaj"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Događaj nije pronađen"
     *     )
     * )
     */
    public function show($id)
    {
        return Event::query()
            ->with(['category:idCategory,name'])
            ->withCount([
                'eventParticipations as registeredCount' => function ($q) {
                    $q->where('status', 'REGISTERED');
                },
            ])
            ->where('idEvent', $id)
            ->firstOrFail();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/events/{id}",
     *     summary="Izmena postojećeg događaja",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID događaja (idEvent)",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="idUser", type="integer", example=1),
     *             @OA\Property(property="idCategory", type="integer", example=2),
     *             @OA\Property(property="title", type="string", example="Izmenjena IT Konferencija 2026"),
     *             @OA\Property(property="description", type="string", example="Ažuriran opis događaja."),
     *             @OA\Property(property="location", type="string", example="Novi Sad"),
     *             @OA\Property(property="startAt", type="string", format="date-time", example="2026-04-20 19:00:00"),
     *             @OA\Property(property="endAt", type="string", format="date-time", example="2026-04-20 22:00:00"),
     *             @OA\Property(property="capacity", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="AKTIVAN")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Događaj uspešno izmenjen"
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
     *         description="Događaj nije pronađen"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Greška validacije"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        //
        $event = Event::where('idEvent', $id)->firstOrFail();
        if (! $event) {
            return response()->json([
                'message' => 'Događaj nije pronađen',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'startAt' => 'sometimes|date',
            'endAt' => 'sometimes|date|after_or_equal:startAt',
            'location' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:ACTIVE,CANCELLED,DRAFT,FINISHED',
            'idCategory' => 'sometimes|integer|exists:categories,idCategory',
            'idUser' => 'sometimes|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();
        $event->update($data);
        $event->refresh();

        $type = (($data['status'] ?? null) === 'CANCELLED')
            ? NotificationType::CANCELLATION
            : NotificationType::UPDATE;
        $message = $type === NotificationType::CANCELLATION
            ? 'Događaj "'.$event->title.'" je otkazan.'
            : 'Događaj "'.$event->title.'" je izmenjen.';

        $this->notifyRegisteredParticipants($event, $message, $type);

        return response()->json([
            $event,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/events/{id}",
     *     summary="Brisanje događaja",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID događaja (idEvent)",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Događaj uspešno obrisan"
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
     *         description="Događaj nije pronađen"
     *     )
     * )
     */
    public function destroy($id)
    {
        //
        $event = Event::where('idEvent', $id)->first();
        if (! $event) {
            return response()->json([
                'message' => 'Događaj nije pronađen',
            ], 404);
        }

        $this->notifyRegisteredParticipants(
            $event,
            'Događaj "'.$event->title.'" je obrisan.',
            NotificationType::CANCELLATION
        );

        $event->delete();

        return response()->json([
            'message' => 'Događaj je obrisan',
        ], 200);
    }

    private function notifyRegisteredParticipants(Event $event, string $message, NotificationType $type): void
    {
        $userIds = EventParticipation::query()
            ->where('idEvent', $event->idEvent)
            ->where('status', ParticipationStatus::REGISTERED)
            ->pluck('idUser');

        foreach ($userIds as $userId) {
            Notification::notifyUser((int) $userId, $event->idEvent, $message, $type);
        }
    }
}
