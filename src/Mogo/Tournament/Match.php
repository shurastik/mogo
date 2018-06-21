<?php
declare(strict_types=1);

namespace Mogo\Tournament;

use Doctrine\ORM\Mapping as ORM;
use Mogo\Exception\IllegalStateException;
use Mogo\Team;
use Mogo\Tournament;
use Mogo\Tournament\Match\Result;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Match
 * @package Mogo
 * @ORM\Entity
 * @ORM\Table(name="matches")
 * @ORM\InheritanceType(value="SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap(value={"tournament" = "Mogo\Tournament\Match\RegularMatch","playoff" = "Mogo\Tournament\Match\PlayOffMatch"})
 */
abstract class Match
{
    /**
     * @var UuidInterface
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     */
    protected $id;
    /**
     * @var Tournament
     * @ORM\ManyToOne(targetEntity="Mogo\Tournament")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id", nullable=false)
     */
    protected $tournament;
    /**
     * @var TournamentTeam
     * @ORM\ManyToOne(targetEntity="Mogo\Tournament\TournamentTeam", cascade={"PERSIST"})
     */
    protected $first;
    /**
     * @var TournamentTeam
     * @ORM\ManyToOne(targetEntity="Mogo\Tournament\TournamentTeam", cascade={"PERSIST"})
     */
    protected $second;
    /**
     * @var Result
     * @ORM\Embedded(class="Mogo\Tournament\Match\Result")
     */
    protected $result;

    /**
     * Match constructor.
     * @param Tournament $tournament
     */
    public function __construct(Tournament $tournament) {
        $this->id = Uuid::uuid4();
        $this->tournament = $tournament;
        $this->result = Result::nullResult();
    }

    /**
     * @return TournamentTeam|null
     */
    public function getFirst(): ?TournamentTeam
    {
        return $this->first;
    }

    /**
     * @return TournamentTeam|null
     */
    public function getSecond(): ?TournamentTeam
    {
        return $this->second;
    }

    /**
     * @return Result|null
     */
    public function getResult(): ?Result
    {
        return $this->result->isEmpty() ? null : $this->result;
    }

    /**
     * @param Result $result
     */
    public function complete(Result $result): void
    {
        if ($this->isCompleted()) {
            throw new IllegalStateException('Match already completed');
        }
        $this->result = $result;
    }

    /**
     * @param Team $team1
     * @param Team $team2
     * @return bool
     */
    public function containsTeams(Team $team1, Team $team2): bool
    {
        return ($this->first->getTeam() === $team1 && $this->second->getTeam() === $team2) ||
            ($this->first->getTeam() === $team2 && $this->second->getTeam() === $team1);
    }

    /**
     * @return TournamentTeam
     */
    public function getWinner(): TournamentTeam
    {
        if (!$this->isCompleted()) {
            throw new IllegalStateException('Match is not completed yet');
        }

        return $this->result->getFirstScore() > $this->result->getSecondScore() ? $this->first : $this->second;
    }
    /**
     * @return TournamentTeam
     */
    public function getLoser(): TournamentTeam
    {
        if (!$this->isCompleted()) {
            throw new IllegalStateException('Match is not completed yet');
        }

        return $this->result->getFirstScore() > $this->result->getSecondScore() ? $this->second : $this->first;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return !$this->result->isEmpty();
    }
}
