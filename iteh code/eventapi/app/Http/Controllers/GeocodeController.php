<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class GeocodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/geocode",
     *     summary="Geokodiranje lokacije preko OpenStreetMap Nominatim",
     *     tags={"Geocode"},
     *
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=true,
     *         description="Naziv ili adresa lokacije",
     *
     *         @OA\Schema(type="string", example="Beograd")
     *     ),
     *
     *     @OA\Response(response=200, description="Koordinate pronađene"),
     *     @OA\Response(response=404, description="Lokacija nije pronađena"),
     *     @OA\Response(response=422, description="Neispravan upit")
     * )
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prošla',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = $validator->validated()['q'];

        $response = Http::timeout(8)
            ->withHeaders([
                'User-Agent' => 'EventHub-ITEH/1.0 (student project)',
                'Accept' => 'application/json',
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'jsonv2',
                'limit' => 1,
                'q' => $query,
            ]);

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Geokodiranje trenutno nije dostupno',
            ], 502);
        }

        $first = $response->json()[0] ?? null;

        if (! is_array($first) || ! isset($first['lat'], $first['lon'])) {
            return response()->json([
                'message' => 'Lokacija nije pronađena',
            ], 404);
        }

        return response()->json([
            'lat' => (float) $first['lat'],
            'lon' => (float) $first['lon'],
            'label' => $first['display_name'] ?? $query,
        ]);
    }
}
