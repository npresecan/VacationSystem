<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function resetPassword(Employee $employee, string $hashedPassword): void
    {
        $employee->setPassword($hashedPassword);
        $employee->setResetToken(null);
        $employee->setTokenExpiry(null);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($employee);
        $entityManager->flush();
    }
}
