<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $job1 = new Job();
        $job1->setName('Backend Developer');
        $manager->persist($job1);

        $job2 = new Job();
        $job2->setName('Frontend Developer');
        $manager->persist($job2);

        $manager->flush();
    }
}
