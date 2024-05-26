<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\User;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'company_name' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'firm_type' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'pan_number' => $this->faker->regexify('[A-Za-z0-9]{15}'),
            'client_code' => $this->faker->regexify('[A-Za-z0-9]{32}'),
            'client_name' => $this->faker->regexify('[A-Za-z0-9]{128}'),
            'aadhar_number' => $this->faker->regexify('[A-Za-z0-9]{12}'),
            'mobile' => $this->faker->regexify('[A-Za-z0-9]{15}'),
            'whatsapp' => $this->faker->regexify('[A-Za-z0-9]{15}'),
            'email' => $this->faker->safeEmail(),
            'alternate_email' => $this->faker->regexify('[A-Za-z0-9]{254}'),
            'website' => $this->faker->regexify('[A-Za-z0-9]{128}'),
            'address' => $this->faker->regexify('[A-Za-z0-9]{255}'),
            'city' => $this->faker->city(),
            'state' => $this->faker->regexify('[A-Za-z0-9]{64}'),
            'country' => $this->faker->country(),
            'pincode' => $this->faker->regexify('[A-Za-z0-9]{10}'),
            'tan_no' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'cin_no' => $this->faker->regexify('[A-Za-z0-9]{20}'),
            'gstin' => $this->faker->regexify('[A-Za-z0-9]{16}'),
            'auditor_group_id' => User::factory(),
            'billing_at' => $this->faker->regexify('[A-Za-z0-9]{128}'),
            'client_status' => $this->faker->randomElement(["active","inactive"]),
            'user_id' => User::factory(),
        ];
    }
}
