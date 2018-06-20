<?php
declare(strict_types=1);

namespace Mogo\Tournament\Match;

use Doctrine\ORM\Mapping as ORM;
use Mogo\Tournament;
use Mogo\Tournament\Match;

/**
 * Class RegularMatch
 *
 * Regular match has all match-ups from start
 *
 * @package Mogo\Tournament\Match
 *
 * @ORM\Entity()
 */
class RegularMatch extends Match
{
    /**
     * Match constructor.
     * @param Tournament $tournament
     * @param Tournament\PlayOff\MatchUp $matchUp
     */
    public function __construct(
        Tournament $tournament,
        Tournament\PlayOff\MatchUp $matchUp
    ) {
        parent::__construct($tournament);
        $this->first = $matchUp->getFirst();
        $this->second = $matchUp->getSecond();
    }

    /**
     * @inheritdoc
     */
    public function complete(Result $result): void
    {
        parent::complete($result);
//        here also can be some sort of domain events, but IMHO events for domain logic often is evil. it depends.
        $this->getWinner()->increaseScore();
    }
}
