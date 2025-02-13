<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Entity\Request;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendVacationApprovalEmail(Request $vacationRequest)
    {
        $employee = $vacationRequest->getEmployee();

        $email = (new Email())
            ->from('nikolapresecan11@gmail.com')
            ->to($employee->getEmail())
            ->subject('Vacation Request Approved')
            ->html('<p>Your vacation request from ' . $vacationRequest->getStartDate()->format('Y-m-d') . ' to ' . $vacationRequest->getEndDate()->format('Y-m-d') . ' has been approved.</p>');

        $this->mailer->send($email);
    }

    public function sendVacationDeclineEmail(Request $vacationRequest)
    {
        $employee = $vacationRequest->getEmployee();

        $email = (new Email())
            ->from('nikolapresecan11@gmail.com')
            ->to($employee->getEmail())
            ->subject('Vacation Request Declined')
            ->html('<p>Your vacation request from ' . $vacationRequest->getStartDate()->format('Y-m-d') . ' to ' . $vacationRequest->getEndDate()->format('Y-m-d') . ' has been declined.</p>');

        $this->mailer->send($email);
    }

    public function sendResetPasswordEmail(string $to, string $template, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from('nikolapresecan11@gmail.com')
            ->to($to)
            ->subject('Reset password')
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }

    public function sendExportEmail(string $to, string $filePath, string $format): void
    {
        $email = (new Email())
            ->from('nikolapresecan11@gmail.com')
            ->to($to)
            ->subject('Export Employees')
            ->text('Attachment in format: ' . strtoupper($format))
            ->attachFromPath($filePath);

        $this->mailer->send($email);
    }
}