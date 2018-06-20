<?php
declare(strict_types=1);

namespace Mogo;

use Mogo\Dto\TournamentDto;
use Mogo\Repository\TournamentRepository;
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
    private $repository;

    /**
     * TournamentService constructor.
     * @param TournamentRepository $repository
     */
    public function __construct(TournamentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return TournamentDto[]
     */
    public function findAll(): array
    {
        return \array_map(
            [TournamentDto::class, 'from'],
            $this->repository->findBy([], ['name' => 'ASC'])
        );
    }

    public function find(string $id)
    {
        $tournament = $this->repository->find(Uuid::fromString($id));

        return $tournament;
    }
}
