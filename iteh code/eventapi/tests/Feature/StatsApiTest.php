<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\User;
use App\ParticipationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_aggregates_events_and_registrations_by_category(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $workshop = Category::factory()->create(['name' => 'Radionica']);
        $lecture = Category::factory()->create(['name' => 'Predavanje']);

        $first = Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $workshop->idCategory,
            'status' => 'ACTIVE',
        ]);
        Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $workshop->idCategory,
            'status' => 'ACTIVE',
        ]);
        Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $lecture->idCategory,
            'status' => 'CANCELLED',
        ]);

        EventParticipation::create([
            'idUser' => $student->id,
            'idEvent' => $first->idEvent,
            'status' => ParticipationStatus::REGISTERED,
            'registeredAt' => now(),
        ]);

        $this->getJson('/api/stats')
            ->assertOk()
            ->assertJsonPath('totals.events', 3)
            ->assertJsonPath('totals.registrations', 1)
            ->assertJsonFragment([
                'name' => 'Radionica',
                'events' => 2,
                'registrations' => 1,
            ]);
    }
}
