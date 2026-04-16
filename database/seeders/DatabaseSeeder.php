<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\Competence;
use App\Models\Offre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Générer 2 administrateurs
        User::factory(2)->create([
            'role' => 'admin',
        ]);

        // 2. Générer 5 recruteurs avec 2 à 3 offres chacun
        User::factory(5)->create([
            'role' => 'recruteur',
        ])->each(function ($user) {
            // Pour chaque recruteur, on crée un nombre aléatoire d'offres (entre 2 et 3)
            Offre::factory(rand(2, 3))->create([
                'user_id' => $user->id,
            ]);
        });

        // 3. Générer 10 candidats avec profils et compétences
        User::factory(10)->create([
            'role' => 'candidat',
        ])->each(function ($user) {
            
            // Créer un profil lié à ce candidat
            $profil = Profil::factory()->create([
                'user_id' => $user->id,
            ]);

            // Créer 3 compétences et les attacher avec un niveau aléatoire
            $competences = Competence::factory(3)->create();
            foreach ($competences as $competence) {
                $profil->competences()->attach($competence->id, [
                    'niveau' => collect(['debutant', 'intermediaire', 'expert'])->random()
                ]);
            }
        });
    }
}