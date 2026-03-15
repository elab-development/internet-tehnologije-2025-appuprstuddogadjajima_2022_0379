<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(
    name: 'Categories',
    description: 'Rute za upravljanje kategorijama'
)]
#[OA\Get(
    path: '/api/categories',
    summary: 'Prikaz svih kategorija',
    tags: ['Categories'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Uspešno vraćena lista kategorija',
        ),
    ]
)]
#[OA\Post(
    path: '/api/categories',
    summary: 'Kreiranje nove kategorije',
    tags: ['Categories'],
    security: [['bearerAuth' => []]],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name'],
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Konferencije'),
                new OA\Property(property: 'opis', type: 'string', example: 'Događaji tipa konferencija'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Kategorija uspešno kreirana'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Get(
    path: '/api/categories/{id}',
    summary: 'Prikaz jedne kategorije',
    tags: ['Categories'],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID kategorije (idCategory)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Uspešno vraćena kategorija'),
        new OA\Response(response: 404, description: 'Kategorija nije pronađena'),
    ]
)]
#[OA\Put(
    path: '/api/categories/{id}',
    summary: 'Izmena postojeće kategorije',
    tags: ['Categories'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID kategorije (idCategory)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string', example: 'Ažurirane konferencije'),
                new OA\Property(property: 'opis', type: 'string', example: 'Ažurirani opis kategorije'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Kategorija uspešno izmenjena'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Kategorija nije pronađena'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Delete(
    path: '/api/categories/{id}',
    summary: 'Brisanje kategorije',
    tags: ['Categories'],
    security: [['bearerAuth' => []]],
    parameters: [
        new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'ID kategorije (idCategory)',
            schema: new OA\Schema(type: 'integer', example: 1),
        ),
    ],
    responses: [
        new OA\Response(response: 200, description: 'Kategorija uspešno obrisana'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
        new OA\Response(response: 403, description: 'Zabranjen pristup'),
        new OA\Response(response: 404, description: 'Kategorija nije pronađena'),
    ]
)]
class CategoryEndpoints
{
}

