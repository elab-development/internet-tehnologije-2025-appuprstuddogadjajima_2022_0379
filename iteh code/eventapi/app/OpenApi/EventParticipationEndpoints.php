<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Event Participations',
    description: 'Rute za učestvovanja korisnika na događajima'
)]
#[OA\Get(
    path: '/api/event-participations',
    summary: 'Prikaz učestvovanja na događajima',
    description: 'ADMIN/ORGANIZATOR vide sva učestvovanja, ostali vide samo svoja.',
    tags: ['Event Participations'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Uspešno vraćena lista učestvovanja',
        ),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
    ]
)]
#[OA\Post(
    path: '/api/event-participations',
    summary: 'Prijava korisnika na događaj',
    tags: ['Event Participations'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['idEvent', 'status', 'registeredAt'],
            properties: [
                new OA\Property(property: 'idEvent', type: 'integer', example: 5),
                new OA\Property(property: 'status', type: 'string', example: 'REGISTERED'),
                new OA\Property(property: 'registeredAt', type: 'string', format: 'date-time', example: '2026-04-10 12:00:00'),
                new OA\Property(property: 'cancelledAt', type: 'string', format: 'date-time', nullable: true),
                new OA\Property(property: 'attendanceMarkedAt', type: 'string', format: 'date-time', nullable: true),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Učestvovanje uspešno kreirano'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 422, description: 'Greška validacije ili korisnik već prijavljen na događaj'),
    ]
)]
#[OA\Get(
    path: '/api/event-participations/{id}',
    summary: 'Prikaz jednog učestvovanja na događaju',
    tags: ['Event Participations'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID učestvovanja (idParticipation)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Uspešno vraćeno učestvovanje'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Učestvovanje nije pronađeno'),
    ]
)]
#[OA\Put(
    path: '/api/event-participations/{id}',
    summary: 'Izmena učestvovanja na događaju',
    tags: ['Event Participations'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID učestvovanja (idParticipation)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'idEvent', type: 'integer', example: 5),
                new OA\Property(property: 'status', type: 'string', example: 'CANCELLED'),
                new OA\Property(property: 'registeredAt', type: 'string', format: 'date-time', example: '2026-04-10 12:00:00'),
                new OA\Property(property: 'cancelledAt', type: 'string', format: 'date-time', nullable: true),
                new OA\Property(property: 'attendanceMarkedAt', type: 'string', format: 'date-time', nullable: true),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Učestvovanje uspešno izmenjeno'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Učestvovanje nije pronađeno'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Delete(
    path: '/api/event-participations/{id}',
    summary: 'Brisanje učestvovanja na događaju',
    tags: ['Event Participations'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID učestvovanja (idParticipation)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Učestvovanje uspešno obrisano'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Učestvovanje nije pronađeno'),
    ]
)]
class EventParticipationEndpoints
{
}

