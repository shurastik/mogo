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
     * @var bool
     */
    public $playOffStarted;
    /**
     * @var Tournament\Match\PlayOffMatch[][]
     */
    public $playOffGames = [];
    /**
     * @var PlayOffMatchDto
     */
    public $finalMatch;
    /**
     * @var PlayOffMatchDto
     */
    public $thirdPlaceMatch;
    /**
     * @var bool
     */
    public $playOffCompleted;
    /**
     * @var Tournament\TournamentTeam[]
     */
    public $ratingList;

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
        $res->playOffStarted = $tournament->isPlayOffStarted();
        if ($tournament->isPlayOffStarted()) {
            $res->finalMatch = PlayOffMatchDto::from($tournament->getFinalMatch());
            PlayOffMatchDto::splitTree($res->finalMatch, 0, $res->playOffGames);
            $res->playOffGames = \array_reverse($res->playOffGames);
            $res->thirdPlaceMatch = PlayOffMatchDto::from($tournament->getThirdPlaceMatch());
        }
        $res->playOffCompleted = $tournament->isPlayOffComplete();
        if ($tournament->isPlayOffComplete()) {
            $res->ratingList = \array_map([TournamentTeamDto::class, 'from'], $tournament->getFinalRating());
        }

        return $res;
    }
}
