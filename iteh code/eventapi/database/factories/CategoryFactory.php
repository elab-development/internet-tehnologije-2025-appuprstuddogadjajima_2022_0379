<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            ['name' => 'Radionica', 'opis' => 'Praktične radionice za razvoj veština'],
            ['name' => 'Predavanje', 'opis' => 'Gostujuća i stručna predavanja'],
            ['name' => 'Takmičenje', 'opis' => 'Studentska i akademska takmičenja'],
            ['name' => 'Konferencija', 'opis' => 'Veće stručne i akademske konferencije'],
            ['name' => 'Panel diskusija', 'opis' => 'Diskusije sa više govornika'],
            ['name' => 'Seminar', 'opis' => 'Edukativni seminari i obuke'],
            ['name' => 'Studentski događaj', 'opis' => 'Događaji u organizaciji studenata'],
            ['name' => 'Networking', 'opis' => 'Umrežavanje studenata i kompanija'],
            ['name' => 'Kultura i umetnost', 'opis' => 'Kulturni i umetnički događaji'],
            ['name' => 'Humanitarni događaj', 'opis' => 'Humanitarne akcije i događaji'],
        ];

        return fake()->randomElement($categories);
    }
}

