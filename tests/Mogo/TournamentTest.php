<?php
declare(strict_types=1);

namespace Mogo;

use Mogo\Tournament\Match\Result;
use Mogo\Tournament\MatchUpGenerator;
use Mogo\Tournament\TournamentTeam;
use Tests\Mogo\Common\CommonTestCase;

/**
 * Class TournamentTest
 * @package Mogo
 */
class TournamentTest extends CommonTestCase
{
    /**
     * @test
     */
    public function tournamentResults(): void
    {
        $tournament = new Tournament();
        for ($i = 0; $i < 8; $i++) {
            $tournament->addTeam($this->teams[$i], 'A');
        }
        for ($i = 8; $i < 16; $i++) {
            $tournament->addTeam($this->teams[$i], 'B');
        }

        // first 4 teams from each division are best
        for ($i = 0; $i < 8; $i++) {
            for ($j = $i; $j < 8; $j++) {
                if ($i !== $j) {
                    $tournament
                        ->getMatch($this->teams[$i], $this->teams[$j])
                        ->complete(new Result(1, 0));
                }
            }
        }
        for ($i = 8; $i < 16; $i++) {
            for ($j = $i; $j < 16; $j++) {
                if ($i !== $j) {
                    $tournament
                        ->getMatch($this->teams[$i], $this->teams[$j])
                        ->complete(new Result(1, 0));
                }
            }
        }

        self::assertEquals(7, $tournament->getTournamentTeam($this->teams[0])->getTotalScore());
        self::assertEquals(0, $tournament->getTournamentTeam($this->teams[7])->getTotalScore());
    }

    /**
     * @test
     */
    public function playoff(): void
    {
        $tournament = new Tournament();
        $teams = [
            new TournamentTeam('A', new Team('Test 1'), $tournament),
            new TournamentTeam('B', new Team('Test 2'), $tournament),
            new TournamentTeam('A', new Team('Test 2'), $tournament),
            new TournamentTeam('B', new Team('Test 4'), $tournament),
        ];

        $matchUpGenerator = $this->createMock(MatchUpGenerator::class);
        $matchUpGenerator->expects(self::once())
            ->method('create')
            ->willReturn( [
                new Tournament\PlayOff\MatchUp($teams[0], $teams[1]),
                new Tournament\PlayOff\MatchUp($teams[2], $teams[3]),
            ]);
        $tournament->startPlayoff($matchUpGenerator);
        $final = $tournament->getFinalMatch();
        self::assertEquals($teams[0], $final->getLeft()->getFirst());
        self::assertEquals($teams[1], $final->getLeft()->getSecond());
        self::assertEquals($teams[2], $final->getRight()->getFirst());
        self::assertEquals($teams[3], $final->getRight()->getSecond());

        $final->getLeft()->complete(new Result(1, 0));
        $final->getRight()->complete(new Result(0, 1));

        self::assertEquals($teams[0], $final->getFirst());
        self::assertEquals($teams[3], $final->getSecond());

        $final->complete(new Result(5, 10));

        self::assertEquals($teams[3], $final->getWinner());
    }
}
