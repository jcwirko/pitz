<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(\App\Set::class, function (Faker $faker) {
    return [
        'tournament_id' => \App\Tournament::find(rand(2,10000))->id,
        'type' => rand(0,1) == 1 ? 'regular' : 'extra',
        'number' => rand(5, 5000)
    ];
});
