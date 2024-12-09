<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $role1 = new Role();
        $role1->setName('Admin');
        $manager->persist($role1);

        $role2 = new Role();
        $role2->setName('Employee');
        $manager->persist($role2);

        $role3 = new Role();
        $role3->setName('Team leader');
        $manager->persist($role3);

        $role4 = new Role();
        $role4->setName('Project manager');
        $manager->persist($role4);

        $manager->flush();
    }
}
