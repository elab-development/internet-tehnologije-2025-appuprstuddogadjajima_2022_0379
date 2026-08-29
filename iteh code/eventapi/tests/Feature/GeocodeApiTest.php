<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_geocode_returns_coordinates_from_nominatim(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '44.8178',
                    'lon' => '20.4569',
                    'display_name' => 'Beograd, Srbija',
                ],
            ], 200),
        ]);

        $this->getJson('/api/geocode?q=Beograd')
            ->assertOk()
            ->assertJsonPath('lat', 44.8178)
            ->assertJsonPath('lon', 20.4569)
            ->assertJsonPath('label', 'Beograd, Srbija');
    }

    public function test_geocode_returns_not_found_when_nominatim_is_empty(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 200),
        ]);

        $this->getJson('/api/geocode?q=NepostojeciGradXYZ')
            ->assertNotFound();
    }

    public function test_geocode_requires_query(): void
    {
        $this->getJson('/api/geocode')->assertStatus(422);
    }
}
