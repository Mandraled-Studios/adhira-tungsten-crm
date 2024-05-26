<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Task;
use App\Models\TaskType;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'assessment_year' => $this->faker->regexify('[A-Za-z0-9]{30}'),
            'status' => $this->faker->randomElement(["Assigned",""]),
            'duedate' => $this->faker->dateTime(),
            'assigned_user_id' => $this->faker->randomNumber(),
            'frequency_override' => $this->faker->regexify('[A-Za-z0-9]{128}'),
            'billing_value' => $this->faker->randomFloat(2, 0, 999999.99),
            'billing_company' => $this->faker->randomElement(["Adhira Associates",""]),
            'task_type_id' => TaskType::factory(),
            'client_id' => Client::factory(),
        ];
    }
}
