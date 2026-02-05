<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; 
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Category::insert([
    ['name' => 'Radionica', 'opis' => 'Praktične radionice'],
    ['name' => 'Predavanje', 'opis' => 'Gostujuća predavanja'],
    ['name' => 'Takmičenje', 'opis' => 'Studentska takmičenja'],
    ['name' => 'Konferencija', 'opis' => 'Stručne konferencije'],
    ['name' => 'Panel diskusija', 'opis' => 'Diskusije'],
    ['name' => 'Seminar', 'opis' => 'Edukacija'],
    ['name' => 'Studentski događaj', 'opis' => 'Studentske aktivnosti'],
    ['name' => 'Networking', 'opis' => 'Umrežavanje'],
    ['name' => 'Kultura i umetnost', 'opis' => 'Kulturni događaji'],
    ['name' => 'Humanitarni događaj', 'opis' => 'Humanitarne akcije'],
]);
    }
}
