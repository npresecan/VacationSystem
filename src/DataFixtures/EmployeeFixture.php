<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Role;
use App\Entity\Job;
use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EmployeeFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $roleAdminId = 1;
        $roleAdmin = $manager->getRepository(Role::class)->find($roleAdminId);
        
        $employee = new Employee();
        $employee->setFirstName('Nikola')
                 ->setLastName('Presečan')
                 ->setUsername('npresecan')
                 ->setEmail('nikolapresecan@example.com')
                 ->setBirthDate(new \DateTime('2002-11-21'))
                 ->setVacationDays(20)
                 ->setRole($roleAdmin);
                 
        $hashedPassword = $this->passwordHasher->hashPassword($employee, '2111');
        $employee->setPassword($hashedPassword);
        
        $manager->persist($employee);
        $manager->flush();
    }
}