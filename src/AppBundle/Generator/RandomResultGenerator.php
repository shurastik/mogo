<?php
declare(strict_types=1);

namespace AppBundle\Generator;

use Mogo\TournamentService;

/**
 * Class RandomResultGenerator
 * @package AppBundle\Generator
 */
class RandomResultGenerator
{
    /**
     * @var TournamentService
     */
    private $tournamentService;

    /**
     * RandomResultGenerator constructor.
     * @param TournamentService $tournamentService
     */
    public function __construct(TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    /**
     * @param string $id
     * @param string $division
     * @throws \Doctrine\ORM\NoResultException
     */
    public function fillTournamentDivision(string $id, string $division): void
    {
        $tournament = $this->tournamentService->find($id);

    }
}
