<?php
declare(strict_types=1);

namespace AppBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mogo\Team;
use Mogo\Tournament;

/**
 * Class LoadTeams
 * @package AppBundle\DataFixtures
 */
class LoadData implements ORMFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $tournament = new Tournament('Test tournament 2018/2019');
        $manager->persist($tournament);
        for ($i = 1; $i <= 16; $i++) {
            $team = new Team('Test '.$i);
            $manager->persist($team);
            $tournament->addTeam($team, 1 === $i % 2 ? 'A' : 'B');
        }
        $manager->flush();
    }
}
