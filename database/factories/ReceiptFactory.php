<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Receipt;

class ReceiptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Receipt::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'payment_date' => $this->faker->date(),
            'paid_in_full' => $this->faker->boolean(),
            'amount_paid' => $this->faker->randomFloat(2, 0, 999999.99),
            'balance' => $this->faker->randomFloat(2, 0, 999999.99),
            'payment_method' => $this->faker->word(),
            'refunded' => $this->faker->boolean(),
            'invoice_id' => Invoice::factory(),
        ];
    }
}
