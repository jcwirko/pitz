<?php

namespace App\Http\Controllers;

use App\Tournament;
use Illuminate\Support\Facades\DB;


class TournamentsController extends Controller
{

    /*
      Estadisticas de los equipos en un torneo
      las cuales se obtienen solo de las jornadas
      regulares (omiten las jornadas extras), el
      arreglo tiene que estár ordenado del equipo
      que tiene mas puntos(points) al que menos
      [
        {
            {
             "id": 1,                     // id de equipo
             "name": "Leones",            // Nombre de equipo
             "goals": 4,                  // Goles totales del equipo en el torneo
             "received_goals": 5,         // Goles recibidos del equipo en el torneo
             "difference_of_goals": -1,   // Diferencia de goles (goals-received_goals)
             "matches_played": 6,         // El numero total de "matches" con resultado donde participa el equipo en el torneo
             "matches_won": 1,            // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el equipo sea ganador
              "draw_matches": 2,           // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el resultado sea empate
              "matches_lost": 3,           // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el equipo sea el perdedor
              "points": 5,                 // Puntos obtenidos por el equipo en el torneo (3 puntos por partid ganado 1 punto por partido empatado y 0 puntos por partido perdido)
            }
        }
      ]
    */

    public function generalTable(Tournament $tournament)
    {

        $r = DB::table('teams as te')
            ->join('tournament_team as tt', 'tt.team_id', 'te.id')
            ->join('tournaments as to', 'tt.tournament_id', 'to.id')
            ->leftJoin('matches', function ($join) {
                $join->on('te.id', '=', 'matches.team_a_id');
                $join->orOn('te.id', '=', 'matches.team_b_id');
            })
            ->join('results as r', 'r.match_id', 'matches.id')
            ->join('sets', 'sets.id', 'matches.set_id')
            ->select(
                'te.id',                              // id de equipo
                'te.name',                           // Nombre de equipo
                $this->getGoals(),                  // Goles totales del equipo en el torneo
                $this->getReceiveGoals(),          // Goles recibidos del equipo en el torneo
                $this->getDifferenceOfGoals(),    // Diferencia de goles (goals-received_goals)
                $this->getMatchesPlayed(),       // El numero total de "matches" con resultado donde participa el equipo en el torneo
                $this->getMatchesWon(),         // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el equipo sea ganador
                $this->getDrawMatches(),       // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el resultado sea empate
                $this->getMatchesLost(),      // El numero total de "matches" con resultado donde participa el equipo en el torneo donde el equipo sea el perdedor
                $this->getPts()              // Puntos obtenidos por el equipo en el torneo (3 puntos por partid ganado 1 punto por partido empatado y 0 puntos por partido perdido)
            )
            ->groupBy(['to.id', 'te.id', 'te.name'])
            ->where('to.id', $tournament->id)
            ->where(strtolower('sets.type'), 'regular')
            ->orderBy('pts', 'desc')
            ->orderBy('goals', 'desc')
            ->orderBy('matches_won', 'desc')
            ->get();


        print_r(json_encode($r));

        return " "; // LO HAGO DE ESTA FORMA PARA QUE CAPTURE EL DEBUG LA CONSULTA, SINO NO SE PUEDE MEDIR EL TIEMPO EN EL NAVEGADOR
    }


    private function getMatchesWon()
    {
        return DB::raw("
          SUM(
            CASE
                WHEN ( matches.team_a_id = te.id AND r.team_a_goals > r.team_b_goals)
                    OR ( matches.team_b_id = te.id AND r.team_b_goals > r.team_a_goals)
                THEN 1 
                ELSE 0 
            END
          ) 
          AS matches_won
        ");
    }


    private function getDrawMatches()
    {
        return DB::raw("
          SUM(
            CASE
                WHEN ((matches.team_a_id = te.id OR matches.team_b_id) AND r.team_a_goals = r.team_b_goals)
                THEN 1 
                ELSE 0 
            END
          ) 
          AS draw_matches
        ");
    }

    private function getMatchesLost()
    {
        return DB::raw("
          SUM(
            CASE
                WHEN ( matches.team_a_id = te.id AND r.team_a_goals < r.team_b_goals)
                    OR ( matches.team_b_id = te.id AND r.team_b_goals < r.team_a_goals)
                THEN 1 
                ELSE 0 
            END
          ) 
          AS matches_lost
        ");
    }

    private function getDifferenceOfGoals()
    {
        return DB::raw("
          COALESCE(
             (
                SUM(
                    CASE
                        WHEN (matches.team_a_id = te.id) THEN r.team_a_goals
                        WHEN (matches.team_b_id = te.id) THEN r.team_b_goals
                    END
                )
             -
                SUM(
                    CASE
                        WHEN (matches.team_a_id = te.id) THEN r.team_b_goals 
                        WHEN (matches.team_b_id = te.id) THEN r.team_a_goals
                    END
                )
             )
             ,0)
            AS difference_of_goals
        ");
    }

    private function getReceiveGoals()
    {
        return DB::raw("
          COALESCE(
            SUM(
              CASE 
                WHEN (matches.team_a_id = te.id) THEN r.team_b_goals 
                WHEN (matches.team_b_id = te.id) THEN r.team_a_goals 
              END
            )
            ,0
          ) 
          AS receive_goals
        ");
    }

    private function getGoals()
    {
        return DB::raw("
          COALESCE(
              SUM(
                 CASE WHEN (matches.team_a_id = te.id) THEN r.team_a_goals 
                      WHEN (matches.team_b_id = te.id) THEN r.team_b_goals END)
                      ,0)
                 AS Goals
        ");
    }

    /*
     * * * * * *
     */
    private function getMatchesPlayed()
    {
        return DB::raw("
             SUM(
                CASE 
                    WHEN (matches.team_a_id = te.id OR matches.team_b_id = te.id)
                    THEN 1 
                    ELSE 0 
                END
             ) 
             AS matches_played
        ");
    }

    private function getPts()
    {
        return DB::raw("
          SUM(
             CASE 
                WHEN(
                   matches.team_a_id = te.id AND r.team_a_goals > r.team_b_goals) 
                   OR (matches.team_b_id = te.id AND r.team_a_goals < r.team_b_goals) 
                THEN 3 
                ELSE 0 
             END) 
          +
          SUM(
            CASE 
               WHEN (matches.team_a_id = te.id OR matches.team_b_id = te.id) 
                     AND (r.team_a_goals = r.team_b_goals) 
               THEN 1 
               ELSE 0 
            END)   
           as pts");
    }


}
