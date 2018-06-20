<?php
declare(strict_types=1);

namespace Mogo\Tournament\MatchUpGenerator;

use Doctrine\Common\Collections\ArrayCollection;
use Mogo\Team;
use Mogo\Tournament;
use Mogo\Tournament\TournamentTeam;
use PHPUnit\Framework\TestCase;

/**
 * Class ChristmasTreeMatchUpGeneratorTest
 * @package Mogo\Tournament\MatchUpGenerator
 */
class ChristmasTreeMatchUpGeneratorTest extends TestCase
{
    /**
     * @param string $division
     * @param int $score
     * @return TournamentTeam
     */
    private function createTeam(string $division, int $score): TournamentTeam
    {
        $team = new TournamentTeam(
            $division,
            $this->createMock(Team::class),
            $this->createMock(Tournament::class)
        );
        while ($score-- > 0) {
            $team->increaseScore();
        }

        return $team;
    }

    /**
     * @test
     */
    public function create(): void
    {
        $teams = [
            $this->createTeam('A', 1),
            $this->createTeam('B', 8),
            $this->createTeam('A', 5),
            $this->createTeam('B', 2),
            $this->createTeam('A', 3),
            $this->createTeam('B', 3),
            $this->createTeam('A', 7),
        ];

        $generator = new ChristmasTreeMatchUpGenerator(2);
        /** @var Tournament\PlayOff\MatchUp[] $matchUps */
        $matchUps = $generator->create(new ArrayCollection($teams));
        self::assertEquals($teams[6], $matchUps[0]->getFirst());
        self::assertEquals($teams[5], $matchUps[0]->getSecond());
        self::assertEquals($teams[2], $matchUps[1]->getFirst());
        self::assertEquals($teams[1], $matchUps[1]->getSecond());
    }
}
