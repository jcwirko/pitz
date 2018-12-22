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

$factory->define(\App\Match::class, function (Faker $faker) {
    return [
        'team_a_id' => \App\Team::find(rand(100,3000))->id,
        'team_b_id' => \App\Team::find(rand(100,3000))->id,
        'tournament_id' => \App\Tournament::find(rand(2,10000))->id,
        'set_id' => \App\Set::find(rand(100,5000))->id,
    ];
});
