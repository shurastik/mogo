<?php
declare(strict_types=1);

namespace Mogo\Tournament;

use Mogo\Exception\InvalidArgument;
use Mogo\Team;
use Mogo\Tournament;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class TournamentTeam
 * @package Mogo\Tournament
 */
class TournamentTeam
{
    /**
     * @var UuidInterface
     */
    private $id;
    /**
     * @var Tournament
     */
    private $tournament;
    /**
     * @var string
     */
    private $division;
    /**
     * @var Team
     */
    private $team;
    /**
     * @var int
     */
    private $totalScore = 0;

    /**
     * TournamentTeam constructor.
     * @param string $division
     * @param Team $team
     * @param Tournament $tournament
     */
    public function __construct(string $division, Team $team, Tournament $tournament)
    {
        if (!\in_array($division, Tournament::getDivisions(), true)) {
            throw new InvalidArgument(\sprintf('Division "%s" does not exist', $division));
        }
        $this->id = Uuid::uuid4();
        $this->tournament = $tournament;
        $this->division = $division;
        $this->team = $team;
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return string
     */
    public function getDivision(): string
    {
        return $this->division;
    }

    public function increaseScore(): void
    {
        $this->totalScore++;
    }

    /**
     * @return int
     */
    public function getTotalScore(): int
    {
        return $this->totalScore;
    }
}
