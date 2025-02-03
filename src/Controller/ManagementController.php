<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RequestRepository;

class ManagementController extends AbstractController
{
    #[Route('/management/dashboard', name: 'management_dashboard', methods: ['GET'])]
    public function dashboard(RequestRepository $requestRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('No access.');
        }
        
        $team = $user->getTeam();
        $teamMembers = $team ? $team->getEmployees()->toArray() : [];

        $createdRequests = $requestRepository->findRequestsByEmployees($teamMembers, 'CREATED');

        $approvedRequests = $requestRepository->findRequestsByEmployees($teamMembers, 'APPROVED');

        return $this->render('management/dashboard.html.twig', [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'profile' => $user->getProfilePicture(),
            'team' => $team,
            'teamMembers' => $teamMembers,
            'createdRequests' => $createdRequests,
            'approvedRequests' => $approvedRequests,
        ]);
    }
}