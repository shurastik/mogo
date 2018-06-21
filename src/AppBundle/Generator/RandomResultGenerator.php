<?php
declare(strict_types=1);

namespace AppBundle\Generator;

use Mogo\Dto\MatchDto;
use Mogo\Dto\PlayOffMatchDto;
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

    /**
     * @param string $id
     * @throws \Doctrine\ORM\NoResultException
     */
    public function fillTournamentPlayOff(string $id): void
    {
        while ($match = $this->findIncompleteMatchInTree($this->tournamentService->find($id)->finalMatch)) {
            $this->tournamentService->finishMatch($id, $match->id, $this->resultProvider->provide($match->id));
        }
        $thirdPlaceMatchId = $this->tournamentService->find($id)->thirdPlaceMatch->id;
        $this->tournamentService->finishMatch(
            $id,
            $thirdPlaceMatchId,
            $this->resultProvider->provide($thirdPlaceMatchId)
        );
    }

    /**
     * @param PlayOffMatchDto|null $match
     * @return PlayOffMatchDto|null
     */
    private function findIncompleteMatchInTree(?PlayOffMatchDto $match): ?PlayOffMatchDto
    {
        if (null === $match) {
            return null;
        }
        if ($match->match->first && !$match->match->completed) {
            return $match;
        }
        if ($res = $this->findIncompleteMatchInTree($match->left)) {
            return $res;
        }

        return $this->findIncompleteMatchInTree($match->right);
    }
}
