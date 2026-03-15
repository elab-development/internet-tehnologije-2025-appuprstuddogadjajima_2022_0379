<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Notifications',
    description: 'Rute za notifikacije ulogovanog korisnika'
)]
#[OA\Get(
    path: '/api/notifications',
    summary: 'Prikaz svih notifikacija ulogovanog korisnika',
    tags: ['Notifications'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Uspešno vraćena lista notifikacija',
        ),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
    ]
)]
#[OA\Post(
    path: '/api/notifications',
    summary: 'Kreiranje nove notifikacije',
    tags: ['Notifications'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['idEvent', 'message', 'type'],
            properties: [
                new OA\Property(property: 'idEvent', type: 'integer', example: 5),
                new OA\Property(property: 'message', type: 'string', example: 'Podsetnik: događaj počinje sutra u 18h'),
                new OA\Property(property: 'type', type: 'string', example: 'REMINDER'),
                new OA\Property(property: 'seen', type: 'boolean', example: false),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Notifikacija uspešno kreirana'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Get(
    path: '/api/notifications/{id}',
    summary: 'Prikaz jedne notifikacije',
    tags: ['Notifications'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID notifikacije (idNotification)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Uspešno vraćena notifikacija'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Notifikacija nije pronađena'),
    ]
)]
#[OA\Put(
    path: '/api/notifications/{id}',
    summary: 'Izmena postojeće notifikacije',
    tags: ['Notifications'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID notifikacije (idNotification)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'idEvent', type: 'integer', example: 5),
                new OA\Property(property: 'message', type: 'string', example: 'Ažurirana poruka notifikacije'),
                new OA\Property(property: 'type', type: 'string', example: 'UPDATE'),
                new OA\Property(property: 'seen', type: 'boolean', example: true),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Notifikacija uspešno izmenjena'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Notifikacija nije pronađena'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Delete(
    path: '/api/notifications/{id}',
    summary: 'Brisanje notifikacije',
    tags: ['Notifications'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID notifikacije (idNotification)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Notifikacija uspešno obrisana'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Notifikacija nije pronađena'),
    ]
)]
class NotificationEndpoints
{
}

