<?php

namespace Database\Factories;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => fake()->jobTitle(), // Un vrai nom de métier (ex: Ingénieur système)
            'description' => fake()->realText(250), // Un paragraphe de description
            'localisation' => fake()->city(), // Une fausse ville
            'type' => fake()->randomElement(['CDI', 'CDD', 'Stage']), // Pioche au hasard parmi tes 3 choix
            'actif' => fake()->boolean(80), // 80% de chance que l'offre soit active (true)
        ];
    }
}
