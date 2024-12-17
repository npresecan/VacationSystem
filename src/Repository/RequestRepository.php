<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }
    
    public function findAllRequestsForEmployee(Employee $employee): array
    {
        return $this->findBy(['employee' => $employee], ['startDate' => 'DESC']);
    }
}
