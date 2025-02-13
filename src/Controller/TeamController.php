<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Employee;
use App\Entity\Role;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/teams')]
final class TeamController extends AbstractController
{
    #[Route(name: 'app_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('admin/teams.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/team/{id}', name: 'app_team_show', methods: ['GET'])]
    public function show(Team $teams, EntityManagerInterface $entityManager): Response
    {
        $employees = $entityManager->getRepository(Employee::class)->findBy(['team' => $teams]);

        return $this->render('admin/teamShow.html.twig', [
            'teams' => $teams,
            'employees' => $employees,
        ]);
    }

    #[Route('/team/{id}/add-members', name: 'app_team_add_members', methods: ['GET', 'POST'])]
    public function addMembers(Team $team, Request $request, TeamRepository $teamRepository, EntityManagerInterface $em): Response
    {
        $employees = $teamRepository->getUnassignedEmployees();
        $teamLeader = $teamRepository->getTeamLeader($team);
        $projectManager = $teamRepository->getProjectManager($team);

        if ($request->isMethod('POST')) {
            if ($request->request->get('team_leader')) {
                $newTeamLeader = $em->getRepository(Employee::class)->find($request->request->get('team_leader'));
                if ($newTeamLeader) {
                    $teamRepository->updateTeamLeader($newTeamLeader, $team);
                    $em->flush();
                }
            }
            
            if ($request->request->get('project_manager')) {
                $newProjectManager = $em->getRepository(Employee::class)->find($request->request->get('project_manager'));
                if ($newProjectManager) {
                    $teamRepository->updateProjectManager($newProjectManager, $team);
                    $em->flush();
                }
            }
            
            $members = $request->request->all()['members'];

            if (is_array($members)) {
                $teamRepository->addTeamMembers($members, $team);
            }
    
            return $this->redirectToRoute('app_team_show', ['id' => $team->getId()]);
        }
    
        return $this->render('admin/teamAddMembers.html.twig', [
            'team' => $team,
            'employees' => $employees,
            'teamLeader' => $teamLeader,
            'projectManager' => $projectManager,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }
}
