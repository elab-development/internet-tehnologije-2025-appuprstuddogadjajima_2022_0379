<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();

        $this->call(CategorySeeder::class);

        Event::factory()->count(20)->create();

        EventParticipation::factory()->count(40)->create();

        Notification::factory()->count(50)->create();
    }
}