<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'EventHub API',
    description: 'API dokumentacija za EventHub aplikaciju'
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'Local server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Post(
    path: '/api/register',
    summary: 'Registracija korisnika',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['firstName', 'lastName', 'email', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'firstName', type: 'string', example: 'Stefan'),
                new OA\Property(property: 'lastName', type: 'string', example: 'Malbasa'),
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'stefan@gmail.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 201, description: 'Uspešna registracija'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Post(
    path: '/api/login',
    summary: 'Prijava korisnika',
    tags: ['Auth'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'stefan@gmail.com'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: 'Uspešna prijava'),
        new OA\Response(response: 401, description: 'Neispravni kredencijali'),
        new OA\Response(response: 422, description: 'Greška validacije'),
    ]
)]
#[OA\Post(
    path: '/api/logout',
    summary: 'Odjava korisnika',
    tags: ['Auth'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Uspešna odjava'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
    ]
)]
#[OA\Get(
    path: '/api/me',
    summary: 'Podaci o trenutno ulogovanom korisniku',
    tags: ['Auth'],
    security: [['bearerAuth' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Podaci o korisniku'),
        new OA\Response(response: 401, description: 'Neautorizovan pristup'),
    ]
)]
class AuthEndpoints
{
}