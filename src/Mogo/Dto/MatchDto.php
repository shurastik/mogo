<?php
declare(strict_types=1);

namespace Mogo\Dto;

use Mogo\Tournament\Match;

/**
 * Class MatchDto
 * @package Mogo\Dto
 */
class MatchDto
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $firstId;
    /**
     * @var string
     */
    public $secondId;
    /**
     * @var int|null
     */
    public $firstScore;
    /**
     * @var int|null
     */
    public $secondScore;
    /**
     * @var bool
     */
    public $completed = false;

    public static function from(Match $match): self
    {
        $res = new self();
        $res->id = $match->getId()->toString();
        $res->firstId = $match->getFirst()->getId()->toString();
        $res->secondId = $match->getSecond()->getId()->toString();
        if ($match->isCompleted()) {
            $res->completed = true;
            $res->firstScore = $match->getResult()->getFirstScore();
            $res->secondScore = $match->getResult()->getSecondScore();
        }

        return $res;
    }
}
