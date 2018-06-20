<?php
declare(strict_types=1);

namespace Mogo\Tournament;

use Mogo\Exception\IllegalStateException;
use Mogo\Team;
use Mogo\Tournament;
use Mogo\Tournament\Match\Result;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Match
 * @package Mogo
 */
abstract class Match
{
    /**
     * @var UuidInterface
     */
    protected $id;
    /**
     * @var Tournament
     */
    protected $tournament;
    /**
     * @var TournamentTeam
     */
    protected $first;
    /**
     * @var TournamentTeam
     */
    protected $second;
    /**
     * @var Result
     */
    protected $result;

    /**
     * Match constructor.
     * @param Tournament $tournament
     */
    public function __construct(Tournament $tournament) {
        $this->id = Uuid::uuid4();
        $this->tournament = $tournament;
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
        return $this->result;
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
        return $this->result->getFirstScore() > $this->result->getSecondScore() ? $this->first : $this->second;
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
        return null !== $this->result;
    }
}
