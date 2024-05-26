<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Task;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'invoice_number' => $this->faker->word(),
            'invoice_date' => $this->faker->date(),
            'duedate' => $this->faker->date(),
            'subtotal' => $this->faker->randomFloat(2, 0, 999999.99),
            'tax1' => $this->faker->randomFloat(2, 0, 999999.99),
            'tax2' => $this->faker->randomFloat(2, 0, 999999.99),
            'total' => $this->faker->randomFloat(2, 0, 999999.99),
            'tax1_label' => $this->faker->randomElement(["cgst",""]),
            'tax2_label' => $this->faker->randomElement(["cgst",""]),
            'invoice_status' => $this->faker->randomElement(["paid",""]),
            'task_id' => Task::factory(),
        ];
    }
}
