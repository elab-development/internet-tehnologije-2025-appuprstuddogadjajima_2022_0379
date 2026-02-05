<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Event;
use App\ParticipationStatus;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventParticipation>
 */
class EventParticipationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
                        'idUser' => User::inRandomOrder()->value('id'),
            'idEvent' => Event::inRandomOrder()->value('idEvent'),
            'status' => fake()->randomElement([
                \App\ParticipationStatus::REGISTERED,
                \App\ParticipationStatus::CANCELLED,
                \App\ParticipationStatus::ATTENDED,
            ]),
            'registeredAt' => fake()->dateTimeBetween('-1 years', 'now'),
            'cancelledAt' => null,
            'attendancemarkedAt' => null,
        ];
    }
}
