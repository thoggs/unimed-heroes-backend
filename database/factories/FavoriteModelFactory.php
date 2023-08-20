<?php

namespace Database\Factories;

use App\Models\FavoriteModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class FavoriteModelFactory extends Factory
{
    protected $model = FavoriteModel::class;

    public function definition(): array
    {
        return [
            'hero_id' => $this->faker->randomNumber(),
            'votes' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
