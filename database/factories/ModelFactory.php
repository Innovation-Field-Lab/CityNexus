<?php

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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(CityNexus\CityNexus\Property::class, function (Faker\Generator $faker) {
    $house_number = rand(100, 1000);
    $street = $faker->streetName;
    $street_type = $faker->streetSuffix;
    $unit = rand(1, 20);

    return [
        'full_address' => $house_number . ' ' . $street . ' ' . $street_type . ' ' . $unit,
        'house_number' => $house_number,
        'street_name' => $street,
        'street_type' => $street_type,
        'unit' => $unit,
        'city' => $faker->city,
        'state' => 'MA',
    ];
});

