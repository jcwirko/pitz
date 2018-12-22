<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SampleTournamentDataSeeder::class);
        factory(\App\Tournament::class, 10000)->create();
        factory(\App\Team::class, 10000)->create();
        $this->call(TournamentTeamSeeder::class);
        factory(\App\Set::class, 7000)->create();
        factory(\App\Match::class, 7000)->create();
        factory(\App\Result::class, 7000)->create();

    }
}
