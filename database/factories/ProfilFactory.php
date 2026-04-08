<?php

namespace Database\Factories;

use App\Models\Profil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profil>
 */
class ProfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre'=>$this->faker->jobTitle(),
            'bio'=>$this->faker->paragraph(),
            'localisation'=>$this->faker->city(),
            'disponible'=>$this->faker->boolean(),
            'user_id'=>\App\Models\User::factory(),
        ];
    }
}
