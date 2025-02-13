<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Form\EmployeeEditType;
use App\Repository\EmployeeRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\MailerService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

#[Route('/admin/employees')]
final class EmployeeController extends AbstractController
{
    #[Route(name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): Response
    {
        return $this->render('employee/index.html.twig', [
            'employees' => $employeeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_employee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, RoleRepository $roleRepository, MailerService $mailerService): Response
    {
        $employee = new Employee();
        $role = $roleRepository->find(2); 
        if ($role) {
            $employee->setRole($role);
        }

        $employee->setVacationDays(20);

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($employee, $plainPassword);
            $employee->setPassword($hashedPassword);

            $imageFile = $form->get('profilePicture')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Could not upload file.');
                }
                $employee->setProfilePicture('uploads/profile_images/' . $newFilename);
            }

            $resetToken = bin2hex(random_bytes(32));
            $dateTime = new \DateTime('now', new \DateTimeZone('Europe/Zagreb'));
            $dateTime->modify('+1 hour');

            $employee->setResetToken($resetToken);
            $employee->setTokenExpiry($dateTime);

            $entityManager->persist($employee);
            $entityManager->flush();

            $resetUrl = $this->generateUrl('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);
            $mailerService->sendResetPasswordEmail(
                $employee->getEmail(),
                'email/reset_password.html.twig',
                [
                    'resetUrl' => $resetUrl,
                    'employee' => $employee,
                ]
            );

            return $this->redirectToRoute('admin_employees', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/new.html.twig', [
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(Employee $employee): Response
    {
        return $this->render('employee/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeeEditType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('profilePicture')->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('profile_pictures_directory'), $newFilename);

                if ($employee->getProfilePicture()) {
                    $oldImagePath = $this->getParameter('profile_pictures_directory').'/'.$employee->getProfilePicture();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $employee->setProfilePicture('uploads/profile_images/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_employees', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employee/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['POST'])]
    public function delete(Request $request, Employee $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_employees', [], Response::HTTP_SEE_OTHER);
    }
}