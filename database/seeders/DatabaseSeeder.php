<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\Competence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         User::factory(5)->create()->each(function ($user) {
            // Créer un profil lié à chaque user
            $profil = Profil::factory()->create([
                'user_id' => $user->id,
            ]);

            // Créer 3 compétences et les attacher avec un niveau
            $competences = Competence::factory(3)->create();
            foreach ($competences as $competence) {
                $profil->competences()->attach($competence->id, [
                    'niveau' => collect(['debutant','intermediaire','expert'])->random()
                ]);
            }
        });
    }
}
