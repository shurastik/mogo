<?php
declare(strict_types=1);

namespace Mogo\Tournament;

use Doctrine\ORM\Mapping as ORM;
use Mogo\Exception\InvalidArgument;
use Mogo\Team;
use Mogo\Tournament;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class TournamentTeam
 * @package Mogo\Tournament
 * @ORM\Entity
 * @ORM\Table(name="tournaments_teams")
 */
class TournamentTeam
{
    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    private $id;
    /**
     * @var Tournament
     * @ORM\ManyToOne(targetEntity="Mogo\Tournament", inversedBy="teams", cascade={"PERSIST"})
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
     */
    private $tournament;
    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=false)
     */
    private $division;
    /**
     * @var Team
     * @ORM\ManyToOne(targetEntity="Mogo\Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false)
     */
    private $team;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
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

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->team;
    }
}
