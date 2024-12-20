<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Employee;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RequestRepository;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(RequestRepository $requestRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('No access.');
        }

        $approvedRequests = $requestRepository->findRequestsApproved('APPROVED');

        return $this->render('admin/dashboard.html.twig', [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'approvedRequests' => $approvedRequests,
        ]);
    }

    #[Route('/admin/employees', name: 'admin_employees', methods: ['GET'])]
    public function employees(EntityManagerInterface $entityManager): Response
    {
        $employees = $entityManager->getRepository(Employee::class)->findAll();
        return $this->render('admin/employees.html.twig', [
            'employees' => $employees,ž
        ]);
    }

    #[Route('/admin/teams', name: 'admin_teams', methods: ['GET'])]
    public function teams(EntityManagerInterface $entityManager): Response
    {
        $teams = $entityManager->getRepository(Team::class)->findAll();
        return $this->render('admin/teams.html.twig', [
            'teams' => $teams,
        ]);
    }
}
