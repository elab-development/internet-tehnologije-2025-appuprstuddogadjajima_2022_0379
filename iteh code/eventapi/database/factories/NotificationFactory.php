<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Event;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'message' => fake()->paragraph(),
            'type' => fake()->randomElement([
                \App\NotificationType::REMINDER,
               \App\NotificationType::UPDATE,
                \App\NotificationType::CANCELLATION,
            ]),
            'seen' => fake()->boolean(),
            'idUser' => User::inRandomOrder()->value('id'),
            'idEvent' => Event::inRandomOrder()->value('idEvent'),
        ];
    }
}