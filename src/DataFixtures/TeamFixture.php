<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TeamFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $team1 = new Team();
        $team1->setName('Team A');
        $manager->persist($team1);

        $team2 = new Team();
        $team2->setName('Team B');
        $manager->persist($team2);

        $manager->flush();
    }
}
