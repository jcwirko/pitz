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

$factory->define(\App\Result::class, function (Faker $faker) {
    return [
        'match_id' => \App\Match::find(rand(100,1000)),
        'team_a_goals' => \App\Team::find(rand(100,300))->id,
        'team_b_goals' => \App\Team::find(rand(100,3000))->id
    ];
});
