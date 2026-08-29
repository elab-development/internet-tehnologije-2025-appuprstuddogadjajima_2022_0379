<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\User;
use App\ParticipationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{organizer: User, category: Category}
     */
    private function organizerWithCategory(): array
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $category = Category::factory()->create();

        return compact('organizer', 'category');
    }

    public function test_guest_can_list_events(): void
    {
        ['organizer' => $organizer, 'category' => $category] = $this->organizerWithCategory();

        Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $category->idCategory,
            'status' => 'ACTIVE',
            'title' => 'Javni dogadjaj',
        ]);

        $this->getJson('/api/events')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Javni dogadjaj']);
    }

    public function test_guest_cannot_create_event(): void
    {
        $this->postJson('/api/events', [
            'title' => 'Nedozvoljeno',
        ])->assertUnauthorized();
    }

    public function test_student_cannot_create_event(): void
    {
        ['organizer' => $organizer, 'category' => $category] = $this->organizerWithCategory();
        $student = User::factory()->create(['role' => 'STUDENT']);

        $this->actingAs($student, 'sanctum')->postJson('/api/events', [
            'title' => 'Studentski pokusaj',
            'description' => 'Opis',
            'startAt' => now()->addDay()->toDateTimeString(),
            'endAt' => now()->addDay()->addHours(2)->toDateTimeString(),
            'location' => 'Beograd',
            'capacity' => 10,
            'status' => 'ACTIVE',
            'idCategory' => $category->idCategory,
            'idUser' => $organizer->id,
        ])->assertForbidden();
    }

    public function test_organizator_can_create_event(): void
    {
        ['organizer' => $organizer, 'category' => $category] = $this->organizerWithCategory();

        $response = $this->actingAs($organizer, 'sanctum')->postJson('/api/events', [
            'title' => 'Novi dogadjaj',
            'description' => 'Opis',
            'startAt' => now()->addDay()->toDateTimeString(),
            'endAt' => now()->addDay()->addHours(2)->toDateTimeString(),
            'location' => 'Beograd',
            'capacity' => 30,
            'status' => 'ACTIVE',
            'idCategory' => $category->idCategory,
            'idUser' => $organizer->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('events', [
            'title' => 'Novi dogadjaj',
            'idUser' => $organizer->id,
        ]);
    }

    public function test_cannot_register_when_event_is_full(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $first = User::factory()->create(['role' => 'STUDENT']);
        $second = User::factory()->create(['role' => 'STUDENT']);
        $category = Category::factory()->create();

        $event = Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $category->idCategory,
            'capacity' => 1,
            'status' => 'ACTIVE',
        ]);

        EventParticipation::create([
            'idUser' => $first->id,
            'idEvent' => $event->idEvent,
            'status' => ParticipationStatus::REGISTERED,
            'registeredAt' => now(),
        ]);

        $this->actingAs($second, 'sanctum')->postJson('/api/event-participations', [
            'idEvent' => $event->idEvent,
            'status' => 'REGISTERED',
            'registeredAt' => now()->toDateTimeString(),
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Kapacitet događaja je popunjen');
    }
}
