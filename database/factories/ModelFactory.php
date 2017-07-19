<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Concert::class, function (Faker\Generator $faker) {
    
    return [
       'title' => 'Example Band',
        'subtitle' => 'with The Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Example Theatre',
        'venue_address' => '123 Example Lane',
        'city' => 'Fakeville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some sample additional information.',
    ];
});

$factory->state(App\Concert::class, 'published', function (Faker\Generator $faker) {
    
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'unpublished', function (Faker\Generator $faker) {
    
    return [
        'published_at' => null,
    ];
});

$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    
    return [
        'concert_id' => function () {
            return factory(App\Concert::class)->create()->id;
        },
        
    ];
});

$factory->state(App\Ticket::class, 'reserved', function (Faker\Generator $faker) {
    
    return [
        'reserved_at' => Carbon::now(),
    ];
});

$factory->define(App\Order::class, function (Faker\Generator $faker) {
    
    return [
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'amount' => 5250,
        'email' => 'frank@example.com',
        'card_last_four' => '1234',
    ];
});
