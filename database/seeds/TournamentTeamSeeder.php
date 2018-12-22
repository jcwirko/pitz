<?php

use Illuminate\Database\Seeder;
use App\Team;
use App\Tournament;


class TournamentTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10000; $i++) {
            DB::table('tournament_team')->insert([
                'tournament_id' => Tournament::find(rand(2, 1000))->id,
                'team_id' => Team::find(rand(2, 1000))->id
            ]);
        }

    }


}
