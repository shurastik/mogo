<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament\Match;

/**
 * Class MatchDto
 * @package Mogo\Dto
 */
class MatchDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var TournamentTeamDto|null
     */
    public $first;
    /**
     * @var TournamentTeamDto|null
     */
    public $second;
    /**
     * @var int|null
     */
    public $firstScore;
    /**
     * @var int|null
     */
    public $secondScore;
    /**
     * @var bool
     */
    public $completed = false;

    public static function from(Match $match): self
    {
        $res = new self();
        $res->id = $match->getId()->toString();
        if ($match->getFirst()) {
            $res->first = TournamentTeamDto::from($match->getFirst());
            $res->second = TournamentTeamDto::from($match->getSecond());
        }
        if ($match->isCompleted()) {
            $res->completed = true;
            $res->firstScore = $match->getResult()->getFirstScore();
            $res->secondScore = $match->getResult()->getSecondScore();
        }

        return $res;
    }
}