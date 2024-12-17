<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\Role;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }
    
    public function getTeamLeader(Team $team): ?Employee
    {
        return $this->getEntityManager()->getRepository(Employee::class)->findOneBy([
            'team' => $team,
            'role' => $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => 'Team Leader']),
        ]);
    }

    public function getProjectManager(Team $team): ?Employee
    {
        return $this->getEntityManager()->getRepository(Employee::class)->findOneBy([
            'team' => $team,
            'role' => $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => 'Project Manager']),
        ]);
    }

    public function getUnassignedEmployees(): array
    {
        return $this->getEntityManager()->getRepository(Employee::class)->findBy([
            'team' => null,
            'role' => $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => 'Employee']),
        ]);
    }

    public function updateTeamLeader(Employee $employee, Team $team): void
    {
        $role = $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => 'Team Leader']);
        $employee->setRole($role);
        $employee->setTeam($team);
        $this->getEntityManager()->persist($employee);
    }

    public function updateProjectManager(Employee $employee, Team $team): void
    {
        $role = $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => 'Project Manager']);
        $employee->setRole($role);
        $employee->setTeam($team);
        $this->getEntityManager()->persist($employee);
    }

    public function addTeamMembers(array $employeeIds, Team $team): void
    {
        $employeeRepo = $this->getEntityManager()->getRepository(Employee::class);

        foreach ($employeeIds as $employeeId) {
            $employee = $employeeRepo->find($employeeId);
            if($employee){
                if (!$employee->getTeam()) {
                    $employee->setTeam($team);
                    $this->getEntityManager()->persist($employee);
                }
            }      
        }

        $this->getEntityManager()->flush();
    }
}