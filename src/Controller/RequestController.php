<?php

namespace App\Controller;

use App\Entity\Request; 
use App\Form\RequestType;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employees')]
final class RequestController extends AbstractController
{
    #[Route(name: 'app_request_index', methods: ['GET'])]
    public function index(RequestRepository $requestRepository): Response
    {
        return $this->render('request/index.html.twig', [
            'requests' => $requestRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_request_new', methods: ['GET', 'POST'])]
    public function new(HttpRequest $httpRequest, EntityManagerInterface $entityManager): Response
    {
        $employee = $this->getUser();

        if (!$employee) {
            throw $this->createAccessDeniedException('No access.');
        }

        $request = new Request(); 
        $form = $this->createForm(RequestType::class, $request);
        $form->handleRequest($httpRequest);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $request->setEmployee($employee);
            $request->setStatus('CREATED');
            $request->setCreatedDate(new \DateTime());

            $entityManager->persist($request);
            $entityManager->flush();

            $this->addFlash('success', 'Your vacation request has been submitted successfully.');
            return $this->redirectToRoute('employees_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('request/new.html.twig', [
            'request' => $request,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_request_show', methods: ['GET'])]
    public function show(VacationRequest $request): Response
    {
        return $this->render('request/show.html.twig', [
            'request' => $request,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_request_edit', methods: ['GET', 'POST'])]
    public function edit(HttpRequest $httpRequest, VacationRequest $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RequestType::class, $request);
        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('request/edit.html.twig', [
            'request' => $request,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_request_delete', methods: ['POST'])]
    public function delete(HttpRequest $httpRequest, VacationRequest $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$request->getId(), $httpRequest->request->get('_token'))) {
            $entityManager->remove($request);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_request_index', [], Response::HTTP_SEE_OTHER);
    }
}