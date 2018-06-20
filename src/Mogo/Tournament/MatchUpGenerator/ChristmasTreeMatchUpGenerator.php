<?php
declare(strict_types=1);

namespace Mogo\Tournament\MatchUpGenerator;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Mogo\Tournament\MatchUpGenerator;
use Mogo\Tournament\PlayOff\MatchUp;
use Mogo\Tournament\TournamentTeam;

/**
 * Class ChristmasTreeMatchUpGenerator :)
 * @package Mogo\Tournament\MatchUpGenerator
 */
class ChristmasTreeMatchUpGenerator implements MatchUpGenerator
{
    /**
     * @var int
     */
    private $divisionTeamsCount;

    /**
     * ChristmasTreeMatchUpGenerator constructor.
     * @param int $divisionTeamsCount
     */
    public function __construct(int $divisionTeamsCount)
    {
        $this->divisionTeamsCount = $divisionTeamsCount;
    }

    /**
     * @inheritdoc
     */
    public function create(Selectable $teams): array
    {
        $divisionATeams = $teams->matching($this->createBestDivisionTeamsCriteria('A'));
        $divisionBTeams = $teams->matching($this->createBestDivisionTeamsCriteria('B'));

        $res = [];
        foreach ($divisionATeams as $i => $team) {
            $res[] = new MatchUp($team, $divisionBTeams->get($this->divisionTeamsCount - $i - 1));
        }

        return $res;
    }

    /**
     * @param string $division
     * @return Criteria
     */
    private function createBestDivisionTeamsCriteria(string $division): Criteria
    {
        return Criteria::create()
            ->where(Criteria::expr()->eq('division', $division))
            ->orderBy(['totalScore' => Criteria::DESC])
            ->setMaxResults($this->divisionTeamsCount);
    }
}
