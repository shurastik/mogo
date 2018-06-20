<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

use Mogo\Exception\InvalidArgument;

/**
 * Class Result
 * @package Mogo\Tournament\Match
 */
class Result
{
    /**
     * @var int
     */
    private $firstScore;
    /**
     * @var int
     */
    private $secondScore;

    /**
     * Result constructor.
     * @param int $firstScore
     * @param int $secondScore
     */
    public function __construct(int $firstScore, int $secondScore)
    {
        if ($firstScore < 0 || $secondScore < 0) {
            throw new InvalidArgument('Score can not be negative.');
        }
        if ($firstScore === $secondScore) {
            throw new InvalidArgument('Draw is not supported.');
        }
        $this->firstScore = $firstScore;
        $this->secondScore = $secondScore;
    }

    /**
     * @return int
     */
    public function getFirstScore(): int
    {
        return $this->firstScore;
    }

    /**
     * @return int
     */
    public function getSecondScore(): int
    {
        return $this->secondScore;
    }
}
