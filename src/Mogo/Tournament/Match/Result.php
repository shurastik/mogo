<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

use Doctrine\ORM\Mapping as ORM;
use Mogo\Exception\InvalidArgument;

/**
 * Class Result
 * @package Mogo\Tournament\Match
 * @ORM\Embeddable()
 */
class Result
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $firstScore;
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $secondScore;

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

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return false;
    }
}
