<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\TaskCheckpoint;
use App\Models\WorkLog;

class WorkLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->text(),
            'task_checkpoint_id' => TaskCheckpoint::factory(),
        ];
    }
}
