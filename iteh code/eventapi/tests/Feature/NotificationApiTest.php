<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\Notification;
use App\Models\User;
use App\NotificationType;
use App\ParticipationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(User $organizer): Event
    {
        $category = Category::factory()->create();

        return Event::factory()->create([
            'idUser' => $organizer->id,
            'idCategory' => $category->idCategory,
            'capacity' => 20,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_registering_creates_notification_for_student(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        $this->actingAs($student, 'sanctum')->postJson('/api/event-participations', [
            'idEvent' => $event->idEvent,
            'status' => 'REGISTERED',
            'registeredAt' => now()->toDateTimeString(),
        ])->assertCreated();

        $this->assertDatabaseHas('notifications', [
            'idUser' => $student->id,
            'idEvent' => $event->idEvent,
            'seen' => 0,
        ]);
    }

    public function test_student_can_list_own_participations_with_event_via_mine_filter(): void
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

        $this->actingAs($student, 'sanctum')
            ->getJson('/api/event-participations?mine=1')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.event.title', $event->title);
    }

    public function test_student_can_mark_notification_as_seen(): void
    {
        $organizer = User::factory()->create(['role' => 'ORGANIZATOR']);
        $student = User::factory()->create(['role' => 'STUDENT']);
        $event = $this->makeEvent($organizer);

        $notification = Notification::notifyUser(
            $student->id,
            $event->idEvent,
            'Test poruka',
            NotificationType::UPDATE
        );

        $this->actingAs($student, 'sanctum')
            ->putJson('/api/notifications/'.$notification->idNotification, ['seen' => true])
            ->assertOk()
            ->assertJsonPath('seen', true);
    }

    public function test_event_update_notifies_registered_participants(): void
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

        $this->actingAs($organizer, 'sanctum')
            ->putJson('/api/events/'.$event->idEvent, ['title' => 'Nova verzija'])
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'idUser' => $student->id,
            'idEvent' => $event->idEvent,
            'type' => NotificationType::UPDATE->value,
        ]);
    }
}
