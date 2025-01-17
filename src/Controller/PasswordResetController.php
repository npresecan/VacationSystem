<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordResetController extends AbstractController
{
    #[Route('/reset-password/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(string $token, Request $request, EmployeeRepository $employeeRepository, UserPasswordHasherInterface $passwordHasher): Response 
    {
        $employee = $employeeRepository->findOneBy(['resetToken' => $token]);

        if (!$employee || $employee->getTokenExpiry() < new \DateTime()) {
            throw $this->createNotFoundException('Invalid or expired token.');
        }

        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class, ['label' => 'New Password'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($employee, $newPassword);

            $employeeRepository->resetPassword($employee, $hashedPassword);

            return $this->redirectToRoute('login');
        }

        return $this->render('employee/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}