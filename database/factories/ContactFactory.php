<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name'   => $this->faker->name(),
            'email'  => $this->faker->unique()->userName().'@gmail.com',
            'phone'  => $this->faker->unique()->numerify('##########'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
        ];
    }
}
