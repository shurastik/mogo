<?php
declare(strict_types=1);

namespace AppBundle\Generator;

use Mogo\Dto\MatchDto;
use Mogo\Tournament\Match\ResultProvider;
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
     * @var ResultProvider
     */
    private $resultProvider;

    /**
     * RandomResultGenerator constructor.
     * @param TournamentService $tournamentService
     * @param ResultProvider $resultProvider
     */
    public function __construct(TournamentService $tournamentService, ResultProvider $resultProvider)
    {
        $this->tournamentService = $tournamentService;
        $this->resultProvider = $resultProvider;
    }

    /**
     * @param string $id
     * @param string $division
     * @throws \Doctrine\ORM\NoResultException
     */
    public function fillTournamentDivision(string $id, string $division): void
    {
        $tournament = $this->tournamentService->find($id);
        foreach ($tournament->divisions[$division] as $match) { /** @var MatchDto $match */
            if (!$match->completed) {
                $this->tournamentService->finishMatch(
                    $id,
                    $match->id,
                    $this->resultProvider->provide($match->id)
                );
            }
        }
    }
}
