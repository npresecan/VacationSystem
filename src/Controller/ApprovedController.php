<?php

namespace App\Controller;

use App\Entity\Approved;
use App\Entity\Request;
use App\Entity\Employee;
use App\Service\MailerService;
use App\Form\ApprovedType;
use App\Repository\ApprovedRepository;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('')]
final class ApprovedController extends AbstractController
{
    // #[Route(name: 'app_approved_index', methods: ['GET'])]
    // public function index(ApprovedRepository $approvedRepository): Response
    // {
    //     return $this->render('approved/index.html.twig', [
    //         'approveds' => $approvedRepository->findAll(),
    //     ]);
    // }

    #[Route('/management/dashboard/approve', name: 'app_approve', methods: ['POST'])]
    public function new(HTTPRequest $request, EntityManagerInterface $entityManager, ApprovedRepository $approvedRepository, RequestRepository $requestRepository, MailerService $mailerService): Response
    {
        $vacationRequestId = $request->request->get('id');
        $status = $request->request->get('status');
        $comment = $request->request->get('comment', null);
        
        $vacationRequest = $entityManager->getRepository(Request::class)->find($vacationRequestId);

        if (!$vacationRequest) {
            return $this->redirectToRoute('management_dashboard', ['error' => 'Request not found']);
        }
        
        $employee = $this->getUser(); 

        $approvedRepository->processApproval($vacationRequest, $employee, $status, new \DateTime(), $comment, $mailerService);
        
        return $this->redirectToRoute('management_dashboard');
    }

    // #[Route('/{id}', name: 'app_approved_show', methods: ['GET'])]
    // public function show(Approved $approved): Response
    // {
    //     return $this->render('approved/show.html.twig', [
    //         'approved' => $approved,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_approved_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Approved $approved, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(ApprovedType::class, $approved);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_approved_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('approved/edit.html.twig', [
    //         'approved' => $approved,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_approved_delete', methods: ['POST'])]
    // public function delete(Request $request, Approved $approved, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$approved->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($approved);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_approved_index', [], Response::HTTP_SEE_OTHER);
    // }
}
