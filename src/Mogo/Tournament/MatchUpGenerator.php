<?php
declare(strict_types=1);

namespace Mogo\Tournament;

use Doctrine\Common\Collections\Selectable;
use Mogo\Tournament\PlayOff\MatchUp;

/**
 * Interface MatchUpMaker
 * @package Mogo\Tournament
 */
interface MatchUpGenerator
{
    /**
     * @param Selectable|TournamentTeam[] $teams
     * @return MatchUp[]
     */
    public function create(Selectable $teams): array;
}
