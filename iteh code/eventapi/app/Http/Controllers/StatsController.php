<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\ParticipationStatus;
use OpenApi\Annotations as OA;

class StatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/stats",
     *     summary="Statistika događaja i prijava za grafikone",
     *     tags={"Stats"},
     *
     *     @OA\Response(response=200, description="Agregirani podaci")
     * )
     */
    public function index()
    {
        $events = Event::query()
            ->with(['category:idCategory,name'])
            ->withCount([
                'eventParticipations as registeredCount' => function ($query) {
                    $query->where('status', ParticipationStatus::REGISTERED);
                },
            ])
            ->get();

        $byCategory = $events
            ->groupBy(fn (Event $event) => $event->category->name ?? 'Bez kategorije')
            ->map(fn ($group, $name) => [
                'name' => (string) $name,
                'events' => $group->count(),
                'registrations' => (int) $group->sum('registeredCount'),
            ])
            ->sortByDesc('events')
            ->values();

        $byStatus = $events
            ->groupBy(function (Event $event) {
                $status = $event->status;

                return $status instanceof \BackedEnum ? $status->value : (string) $status;
            })
            ->map(fn ($group, $status) => [
                'status' => (string) $status,
                'events' => $group->count(),
            ])
            ->values();

        return response()->json([
            'totals' => [
                'events' => $events->count(),
                'registrations' => (int) $events->sum('registeredCount'),
            ],
            'byCategory' => $byCategory,
            'byStatus' => $byStatus,
        ]);
    }
}
