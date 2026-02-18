<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'type' => 'call',
            'status' => 'open',
            'due_at' => now()->addDay(),
            'assigned_to' => User::factory(),
            'customer_id' => null,
            'lead_id' => null,
        ];
    }
}
