<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament\TournamentTeam;

/**
 * Class TeamDto
 * @package Mogo\Dto
 */
class TournamentTeamDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $division;
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $totalScore;

    /**
     * @param TournamentTeam $team
     * @return TournamentTeamDto
     */
    public static function from(TournamentTeam $team): self
    {
        $res = new self();
        $res->id = $team->getId()->toString();
        $res->division = $team->getDivision();
        $res->name = (string)$team;
        $res->totalScore = $team->getTotalScore();

        return $res;
    }
}
