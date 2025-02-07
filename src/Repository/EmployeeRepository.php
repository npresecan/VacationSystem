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

    public function findByFilters(array $filters)
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.role', 'r')
            ->leftJoin('e.team', 't')
            ->leftJoin('e.job', 'j')
            ->addSelect('r', 't', 'j');

        $filters = array_filter($filters);

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'firstName':
                case 'lastName':
                case 'email':
                case 'username':
                    $qb->andWhere("LOWER(e.$key) LIKE LOWER(:$key)")
                    ->setParameter($key, '%' . $value . '%');
                    break;

                case 'birthDate':
                    try {
                        $qb->andWhere("e.birthDate = :birthDate")
                        ->setParameter('birthDate', new \DateTime($value));
                    } catch (\Exception $e) {
                        throw new \InvalidArgumentException("Invalid birthDate format. Use YYYY-MM-DD.");
                    }
                    break;

                case 'vacationDays':
                    $qb->andWhere("e.vacationDays = :vacationDays")
                    ->setParameter('vacationDays', (int) $value);
                    break;

                case 'role':
                    $qb->andWhere("LOWER(r.name) LIKE LOWER(:role)")
                    ->setParameter('role', '%' . $value . '%');
                    break;

                case 'team':
                    $qb->andWhere("LOWER(t.name) LIKE LOWER(:team)")
                    ->setParameter('team', '%' . $value . '%');
                    break;

                case 'job':
                    $qb->andWhere("LOWER(j.name) LIKE LOWER(:job)")
                    ->setParameter('job', '%' . $value . '%');
                    break;
            }
        }

        return $qb->getQuery()->getResult();
    }
}