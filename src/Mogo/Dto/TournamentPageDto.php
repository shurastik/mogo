<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament;

/**
 * Class TournamentPageDto
 * @package Mogo\Dto
 */
class TournamentPageDto
{
    /**
     * @var TournamentDto
     */
    public $tournament;
    /**
     * @var TournamentTeamDto[]
     */
    public $teams = [];
    /**
     * @var MatchDto[][]
     */
    public $divisions = [];

    /**
     * @param Tournament $tournament
     * @return TournamentPageDto
     */
    public static function from(Tournament $tournament): self
    {
        $res = new self();
        $res->tournament = TournamentDto::from($tournament);
        $teams = $tournament->getTeams()->toArray();
        usort(
            $teams,
            function (Tournament\TournamentTeam $a, Tournament\TournamentTeam $b) {
                return (string)$a->getTeam() <=> (string)$b->getTeam();
            }
        );
        foreach ($teams as $team) {
            $res->teams[] = TournamentTeamDto::from($team);
        }
        foreach (Tournament::getDivisions() as $division) {
            $res->divisions[$division] = $tournament->getDivisionMatches($division)
                ->map(\Closure::fromCallable([MatchDto::class, 'from']));
        }

        return $res;
    }
}
