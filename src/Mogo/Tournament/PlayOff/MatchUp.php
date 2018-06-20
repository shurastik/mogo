<?php
declare(strict_types=1);

namespace Mogo\Tournament\PlayOff;

use Mogo\Exception\IllegalStateException;
use Mogo\Tournament\TournamentTeam;

/**
 * Class MatchUp
 * @package Mogo\Tournament\PlayOff
 */
class MatchUp
{
    /**
     * @var TournamentTeam
     */
    private $first;
    /**
     * @var TournamentTeam
     */
    private $second;

    /**
     * MatchUp constructor.
     * @param TournamentTeam $first
     * @param TournamentTeam $second
     */
    public function __construct(TournamentTeam $first, TournamentTeam $second)
    {
        if ($first === $second) {
            throw new IllegalStateException("Team can't play with self ¯\_(ツ)_/¯");
        }
        $this->first = $first;
        $this->second = $second;
    }

    /**
     * @return TournamentTeam
     */
    public function getFirst(): TournamentTeam
    {
        return $this->first;
    }

    /**
     * @return TournamentTeam
     */
    public function getSecond(): TournamentTeam
    {
        return $this->second;
    }
}
