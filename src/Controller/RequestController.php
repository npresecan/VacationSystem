<?php

namespace App\Controller;

use App\Entity\Request as VacationRequest; // Koristimo alias za entitet kako bismo izbjegli konflikt
use App\Form\RequestType;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest; // Alias za Symfony Request
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/request')]
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
        $request = new VacationRequest(); // Ispravno instanciranje entiteta
        $form = $this->createForm(RequestType::class, $request);
        $form->handleRequest($httpRequest);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($request);
            $entityManager->flush();

            return $this->redirectToRoute('app_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('request/new.html.twig', [
            'request' => $request,
            'form' => $form,
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