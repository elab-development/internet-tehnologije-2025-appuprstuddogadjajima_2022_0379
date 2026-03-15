<?php

namespace App\OpenApi;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="EventHub API",
 *     version="1.0.0",
 *     description="API dokumentacija za EventHub aplikaciju"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class Swagger {}
