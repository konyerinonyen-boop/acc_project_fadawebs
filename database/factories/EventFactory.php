<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     * $faker = \Faker\Factory::create();

     */
    public function definition(): array
    {
        return [
            // Automatically creates or associates a test user ID
            'user_id' =>  1, 
            
            // 💡 If 70% chance passes, pick an existing zone ID (1 or 2); otherwise, assign null cleanly.
'zone_id' => $this->faker->boolean(70) ? \App\Models\Zone::pluck('id')->random() : null,
            
            // Generates a catchy corporate/social event title
            'title' => $this->faker->words(3, true) . ' Event',
            
            // Placeholder filename for your event graphics
            'image' => 'event_' . $this->faker->numberBetween(1, 10) . '.jpg',
            
            // Random styling constraints
            'dress_code' => $this->faker->randomElement(['Formal Corporate', 'All White', 'Business Casual', 'Sportswear']),
            
            // Internet options
            'wifi' => $this->faker->randomElement(['Available', 'None', 'VIP Lounge Only']),
            
            // Generates a clean future date string
            'date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            
            // Realistic background text description
            'description' => $this->faker->paragraph(3),
            
            // Boolean toggle for features (80% false, 20% true)
            'is_featured' => $this->faker->boolean(20),
            
            // Generates realistic building/street landmarks
            'venue' => $this->faker->buildingNumber() . ' ' . $this->faker->streetName() . ', Lagos',
        ];
    }
}