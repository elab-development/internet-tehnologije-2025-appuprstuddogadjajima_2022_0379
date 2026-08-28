<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\User;
use App\ParticipationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventParticipationApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(User $organizer): Event
    {
        $category = Category::factory()->create();

        return Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $category->idCategory,
            'capacity' => 2,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_student_can_register_and_cancel_participation(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        $register = $this->actingAs($student, 'sanctum')->postJson('/api/event-participations', [
            'idEvent' => $event->idEvent,
            'status' => 'REGISTERED',
            'registeredAt' => now()->toDateTimeString(),
        ]);

        $register->assertCreated();
        $this->assertDatabaseHas('event_participations', [
            'idUser' => $student->id,
            'idEvent' => $event->idEvent,
            'status' => ParticipationStatus::REGISTERED->value,
        ]);

        $id = $register->json('idParticipation');

        $cancel = $this->actingAs($student, 'sanctum')->putJson("/api/event-participations/{$id}", [
            'status' => 'CANCELLED',
            'cancelledAt' => now()->toDateTimeString(),
        ]);

        $cancel->assertOk();
        $this->assertDatabaseHas('event_participations', [
            'idParticipation' => $id,
            'status' => ParticipationStatus::CANCELLED->value,
        ]);
    }

    public function test_student_cannot_see_other_users_participations(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $other = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        EventParticipation::create([
            'idUser' => $other->id,
            'idEvent' => $event->idEvent,
            'status' => ParticipationStatus::REGISTERED,
            'registeredAt' => now(),
        ]);

        $response = $this->actingAs($student, 'sanctum')
            ->getJson('/api/event-participations?idEvent='.$event->idEvent);

        $response->assertOk();
        $this->assertCount(0, $response->json());
    }

    public function test_organizator_can_list_event_participants_with_user_data(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create([
            'role' => 'STUDENT',
            'firstName' => 'Ana',
            'lastName' => 'Ilic',
            'email' => 'ana@example.com',
        ]);
        $event = $this->makeEvent($organizer);

        EventParticipation::create([
            'idUser' => $student->id,
            'idEvent' => $event->idEvent,
            'status' => ParticipationStatus::REGISTERED,
            'registeredAt' => now(),
        ]);

        $response = $this->actingAs($organizer, 'sanctum')
            ->getJson('/api/event-participations?idEvent='.$event->idEvent);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.user.email', 'ana@example.com')
            ->assertJsonPath('0.user.firstName', 'Ana');
    }

    public function test_organizator_can_update_and_delete_event(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $event = $this->makeEvent($organizer);

        $update = $this->actingAs($organizer, 'sanctum')->putJson('/api/events/'.$event->idEvent, [
            'title' => 'Izmenjen naslov',
            'location' => 'Novi Sad',
        ]);

        $update->assertOk();
        $this->assertDatabaseHas('events', [
            'idEvent' => $event->idEvent,
            'title' => 'Izmenjen naslov',
            'location' => 'Novi Sad',
        ]);

        $delete = $this->actingAs($organizer, 'sanctum')
            ->deleteJson('/api/events/'.$event->idEvent);

        $delete->assertOk();
        $this->assertDatabaseMissing('events', ['idEvent' => $event->idEvent]);
    }

    public function test_student_cannot_update_event(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        $this->actingAs($student, 'sanctum')
            ->putJson('/api/events/'.$event->idEvent, ['title' => 'Hacking'])
            ->assertForbidden();
    }

    public function test_event_show_includes_registered_count(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        EventParticipation::create([
            'idUser' => $student->id,
            'idEvent' => $event->idEvent,
            'status' => ParticipationStatus::REGISTERED,
            'registeredAt' => now(),
        ]);

        $this->getJson('/api/events/'.$event->idEvent)
            ->assertOk()
            ->assertJsonPath('registeredCount', 1)
            ->assertJsonPath('category.idCategory', $event->idCategory);
    }
}
