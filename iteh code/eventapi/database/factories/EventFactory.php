<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 day', '+1 month');
        $end   = (clone $start)->modify('+' . fake()->numberBetween(1, 4) . ' hours');

        return [
            'title'       => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'location'    => fake()->city(),
            'startAt'     => $start,
            'endAt'       => $end,
            'capacity' => fake()->numberBetween(20, 200),
            'status'      => \App\EventStatus::ACTIVE, //  ENUM, ne string
            'idUser'      => User::inRandomOrder()->value('id'),
            'idCategory'  => Category::inRandomOrder()->value('idCategory'),
        ];
    }
}