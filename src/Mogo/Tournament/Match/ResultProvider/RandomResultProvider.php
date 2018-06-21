<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match\ResultProvider;

use Mogo\Team;
use Mogo\Tournament\Match\Result;
use Mogo\Tournament\Match\ResultProvider;

/**
 * Class RandomResultProvider
 * @package Mogo\Tournament\Match\ResultProvider
 */
class RandomResultProvider implements ResultProvider
{
    /**
     * @var int
     */
    private $maxScore;

    /**
     * RandomResultProvider constructor.
     * @param int $maxScore
     */
    public function __construct(int $maxScore)
    {
        $this->maxScore = $maxScore;
    }

    /**
     * @param string $matchId
     * @return Result
     */
    public function provide(string $matchId): Result
    {
        $hostScore = \random_int(0, $this->maxScore);
        do {
            $guestScore = \random_int(0, $this->maxScore);
        } while ($hostScore === $guestScore);

        return new Result($hostScore, $guestScore);
    }
}
