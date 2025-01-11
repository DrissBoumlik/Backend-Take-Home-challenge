<?php

namespace Domain\Users\database\factories;

use Domain\Users\Models\User;
use Domain\Users\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Users\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{

    protected $model = UserPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? $this->faker->randomNumber(),
            'sources' => $this->faker->randomElements(['Source A', 'Source B', 'Source C'], $this->faker->numberBetween(1, 3)),
            'categories' => $this->faker->randomElements(['Category A', 'Category B', 'Category C'], $this->faker->numberBetween(1, 3)),
            'authors' => $this->faker->randomElements(['Author A', 'Author B', 'Author C'], $this->faker->numberBetween(1, 3)),
        ];
    }
}
