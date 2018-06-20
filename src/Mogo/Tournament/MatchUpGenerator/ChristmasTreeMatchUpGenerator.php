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
        $divisionATeams = $teams->matching($this->createBestDivisionTeamsCriteria('A'))->toArray();
        $divisionBTeams = $teams->matching($this->createBestDivisionTeamsCriteria('B'))->toArray();

        $res = [];
        while ($divisionATeams) {
            $res[] = new MatchUp(\array_shift($divisionATeams), \array_pop($divisionBTeams));
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
