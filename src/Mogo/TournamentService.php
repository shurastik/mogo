<?php
declare(strict_types=1);

namespace Mogo;

use Mogo\Dto\CreateTournamentCommand;
use Mogo\Dto\TournamentDto;
use Mogo\Dto\TournamentPageDto;
use Mogo\Repository\TeamRepository;
use Mogo\Repository\TournamentRepository;
use Mogo\Tournament\Match\Result;
use Mogo\Tournament\MatchUpGenerator;
use Mogo\Tournament\TournamentTeam;
use Ramsey\Uuid\Uuid;

/**
 * Class TournamentService
 * @package Mogo
 */
class TournamentService
{
    /**
     * @var TournamentRepository
     */
    private $tournamentRepository;
    /**
     * @var TeamRepository
     */
    private $teamRepository;
    /**
     * @var MatchUpGenerator
     */
    private $playOffMatchupGenerator;

    /**
     * TournamentService constructor.
     * @param TournamentRepository $repository
     * @param TeamRepository $teamRepository
     * @param MatchUpGenerator $matchUpGenerator
     */
    public function __construct(TournamentRepository $repository, TeamRepository $teamRepository, MatchUpGenerator $matchUpGenerator)
    {
        $this->tournamentRepository = $repository;
        $this->teamRepository = $teamRepository;
        $this->playOffMatchupGenerator = $matchUpGenerator;
    }

    /**
     * @return TournamentDto[]
     */
    public function findAll(): array
    {
        return \array_map(
            [TournamentDto::class, 'from'],
            $this->tournamentRepository->findBy([], ['name' => 'ASC'])
        );
    }

    /**
     * @param string $id
     * @return TournamentPageDto
     * @throws \Doctrine\ORM\NoResultException
     */
    public function find(string $id): TournamentPageDto
    {
        $tournament = $this->tournamentRepository->get(Uuid::fromString($id));

        return TournamentPageDto::from($tournament);
    }

    /**
     * @param CreateTournamentCommand $command
     * @return TournamentDto
     */
    public function create(CreateTournamentCommand $command): TournamentDto
    {
        $tournament = new Tournament($command->name);
        foreach ($this->teamRepository->findBy([], ['name' => 'ASC']) as $i => $team) {
            $tournament->addTeam($team, 0 === $i % 2 ? 'A' : 'B');
        }
        $this->tournamentRepository->save($tournament);

        return TournamentDto::from($tournament);
    }

    /**
     * @param string $id
     * @param string $matchId
     * @param Result $result
     * @throws \Doctrine\ORM\NoResultException
     */
    public function finishMatch(string $id, string $matchId, Result $result): void
    {
        $tournament = $this->tournamentRepository->get(Uuid::fromString($id));
        $tournament->getMatchById(Uuid::fromString($matchId))
            ->complete($result);
        if ($tournament->isPlayOffCanBeStarted()) {
            $tournament->startPlayoff($this->playOffMatchupGenerator);
        }

        $this->tournamentRepository->save($tournament);
    }
}
