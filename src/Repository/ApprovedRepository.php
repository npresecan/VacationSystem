<?php

namespace App\Repository;

use App\Entity\Approved;
use App\Entity\Request;
use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\MailerService;

/**
 * @extends ServiceEntityRepository<Approved>
 */
class ApprovedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Approved::class);
    }
    
    public function processApproval(Request $request, Employee $employee, string $status, \DateTimeInterface $date, ?string $comment, MailerService $mailerService): void
    {
        $approved = $this->getEntityManager()->getRepository(Approved::class)->findOneBy(['request' => $request]);
        if (!$approved) {
            $approved = new Approved();
            $approved->setRequest($request); 
            $this->getEntityManager()->persist($approved);
            $this->getEntityManager()->flush();
        }
        
        if (in_array('ROLE_TEAM LEADER', $employee->getRoles())) {
            $approved->setTeamLeader($employee);  
            $approved->setStatusTeamLeader($status);
            $approved->setDateApprovedTl($date);
            if ($status === 'DECLINED') {
                $approved->setCommentTl($comment);
            }
        }
        
        if (in_array('ROLE_PROJECT MANAGER', $employee->getRoles())) {
            $approved->setProjectManager($employee);
            $approved->setStatusProjectManager($status);
            $approved->setDateApprovedPm($date);
            if ($status === 'DECLINED') {
                $approved->setCommentPm($comment);
            }
        }
        
        if ($approved->getStatusTeamLeader() === 'APPROVED' && $approved->getStatusProjectManager() === 'APPROVED') {
            $this->approveVacationRequest($request);
            $mailerService->sendVacationApprovalEmail($request);
        } elseif ($approved->getStatusTeamLeader() === 'DECLINED' || $approved->getStatusProjectManager() === 'DECLINED') {
            $this->declineVacationRequest($request);
            $mailerService->sendVacationDeclineEmail($request);
        }

        $this->getEntityManager()->flush();
    }

    private function approveVacationRequest(Request $request): void
    {
        $employee = $request->getEmployee();
        $employee->setVacationDays($employee->getVacationDays() - $request->getNumberOfDays());
        $this->getEntityManager()->persist($employee);
        $request->setStatus('APPROVED');
        $this->getEntityManager()->persist($request);
    }

    private function declineVacationRequest(Request $request): void
    {
        $request->setStatus('DECLINED');
        $this->getEntityManager()->persist($request);
    }

    public function findApprovedDetailsForRequests(array $requests): array
    {
        $approvedDetails = [];
        
        foreach ($requests as $request) {
            $approval = $this->findOneBy(['request' => $request]);
            if ($approval) {
                $approvedDetails[$request->getId()] = $approval;
            }
        }
        
        return $approvedDetails;
    }
}