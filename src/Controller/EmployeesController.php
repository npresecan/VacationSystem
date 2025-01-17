<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Team;
use App\Entity\Employee;
use App\Repository\RequestRepository;

class EmployeesController extends AbstractController
{
    #[Route('/employees/dashboard', name: 'employees_dashboard', methods: ['GET'])]
    public function dashboard(RequestRepository $requestRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('No access.');
        }

        $vacdays = 20;
        $remainingVacationDays = $user->getVacationDays();

        $team = $user->getTeam();
        $teamMembers = $team ? $team->getEmployees()->toArray() : [];

        $requests = $requestRepository->findAllRequestsForEmployee($user);

        $approvedRequests = $requestRepository->findRequestsByEmployees($teamMembers, 'APPROVED');

        $groupedApprovedRequests = [];
        foreach ($approvedRequests as $request) {
            $employeeId = $request->getEmployee()->getId();
            if (!isset($groupedApprovedRequests[$employeeId])) {
                $groupedApprovedRequests[$employeeId] = [];
            }
            $groupedApprovedRequests[$employeeId][] = $request;
        }

        return $this->render('employees/dashboard.html.twig', [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'remainingVacationDays' => $remainingVacationDays,
            'team' => $team,
            'teamMembers' => $teamMembers,
            'vacdays' => $vacdays,
            'requests' => $requests,
            'approvedRequests' => $groupedApprovedRequests,
        ]);
    }
}