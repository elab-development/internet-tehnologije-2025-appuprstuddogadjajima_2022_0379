<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Events',
    description: 'Rute za upravljanje događajima'
)]
#[OA\Get(
    path: '/api/events',
    summary: 'Prikaz svih događaja',
    tags: ['Events'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Uspešno vraćena lista događaja',
        ),
    ]
)]
#[OA\Post(
    path: '/api/events',
    summary: 'Kreiranje novog događaja',
    tags: ['Events'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['idUser', 'idCategory', 'title', 'description', 'location', 'startAt', 'endAt', 'capacity', 'status'],
            properties: [
                new OA\Property(property: 'idUser', type: 'integer', example: 1),
                new OA\Property(property: 'idCategory', type: 'integer', example: 2),
                new OA\Property(property: 'title', type: 'string', example: 'IT Konferencija 2026'),
                new OA\Property(property: 'description', type: 'string', example: 'Velika studentska konferencija iz oblasti informacionih tehnologija.'),
                new OA\Property(property: 'location', type: 'string', example: 'Beograd'),
                new OA\Property(property: 'startAt', type: 'string', format: 'date-time', example: '2026-04-15 18:00:00'),
                new OA\Property(property: 'endAt', type: 'string', format: 'date-time', example: '2026-04-15 21:00:00'),
                new OA\Property(property: 'capacity', type: 'integer', example: 150),
                new OA\Property(property: 'status', type: 'string', example: 'AKTIVAN'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Događaj uspešno kreiran'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Get(
    path: '/api/events/{id}',
    summary: 'Prikaz jednog događaja',
    tags: ['Events'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID događaja (idEvent)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Uspešno vraćen događaj'),
        new OA\Response(response: 404, description: 'Događaj nije pronađen'),
    ]
)]
#[OA\Put(
    path: '/api/events/{id}',
    summary: 'Izmena postojećeg događaja',
    tags: ['Events'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID događaja (idEvent)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'idUser', type: 'integer', example: 1),
                new OA\Property(property: 'idCategory', type: 'integer', example: 2),
                new OA\Property(property: 'title', type: 'string', example: 'Izmenjena IT Konferencija 2026'),
                new OA\Property(property: 'description', type: 'string', example: 'Ažuriran opis događaja.'),
                new OA\Property(property: 'location', type: 'string', example: 'Novi Sad'),
                new OA\Property(property: 'startAt', type: 'string', format: 'date-time', example: '2026-04-20 19:00:00'),
                new OA\Property(property: 'endAt', type: 'string', format: 'date-time', example: '2026-04-20 22:00:00'),
                new OA\Property(property: 'capacity', type: 'integer', example: 200),
                new OA\Property(property: 'status', type: 'string', example: 'AKTIVAN'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Događaj uspešno izmenjen'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Događaj nije pronađen'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Delete(
    path: '/api/events/{id}',
    summary: 'Brisanje događaja',
    tags: ['Events'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID događaja (idEvent)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Događaj uspešno obrisan'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Događaj nije pronađen'),
    ]
)]
class EventEndpoints
{
}

