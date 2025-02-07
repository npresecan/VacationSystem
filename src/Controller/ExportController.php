<?php

namespace App\Controller;

use App\Repository\EmployeeRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportController extends AbstractController
{
    #[Route('/admin/export', name: 'admin_export_users', methods: ['GET'])]
    public function exportUsers(Request $request, EmployeeRepository $employeeRepository, CacheInterface $cache, MailerService $mailerService): Response 
    {
        $user = $this->getUser();
        $cacheKey = 'export_limit_' . $user->getId();
        
        $exportCount = $cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(60);
            return 0;
        });
        
        if ($exportCount >= 2) {
            return new Response('Too many requests, try later.', 429);
        }
        
        $cache->delete($cacheKey);
        $cache->get($cacheKey, function (ItemInterface $item) use ($exportCount) {
            $item->expiresAfter(60);
            $item->set($exportCount + 1);
            return $exportCount + 1;
        });
        
        $filters = $request->query->all();
        $employees = $employeeRepository->findByFilters($filters);

        $format = $request->query->get('format', 'csv'); 
        $fileName = 'export_users_' . time();

        if ($format === 'csv') {
            $filePath = $this->generateCSV($employees, $fileName);
        } else {
            $filePath = $this->generatePDF($employees, $fileName);
        }

        $mailerService->sendExportEmail($user->getEmail(), $filePath, $format);

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath));

        return $response;
    }

    private function generateCSV(array $employees, string $fileName): string
    {
        $filePath = sys_get_temp_dir() . '/' . $fileName . '.csv';
        $file = fopen($filePath, 'w');
        
        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, ['#', 'First name', 'Last name', 'Email', 'Username', 'Birth date', 'Team', 'Role', 'Job'], ';');

        $counter = 1;
        foreach ($employees as $employee) {
            fputcsv($file, [
                $counter++,
                $employee->getFirstName(),
                $employee->getLastName(),
                $employee->getEmail(),
                $employee->getUsername(),
                $employee->getBirthDate()?->format('Y-m-d'),
                $employee->getTeam()?->getName() ?? 'N/A',
                $employee->getRole()?->getName() ?? 'N/A',
                $employee->getJob()?->getName() ?? 'N/A',
            ], ';');
        }

        fclose($file);
        return $filePath;
    }

    private function generatePDF(array $employees, string $fileName): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = '<h2>Export Employees</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Team</th>
                <th>Role</th>
                <th>Job</th>
            </tr>';

        $counter = 1;
        foreach ($employees as $employee) {
            $html .= "<tr>
                        <td>{$counter}</td>
                        <td>{$employee->getFirstName()}</td>
                        <td>{$employee->getLastName()}</td>
                        <td>{$employee->getEmail()}</td>
                        <td>{$employee->getTeam()?->getName()}</td>
                        <td>{$employee->getRole()?->getName()}</td>
                        <td>{$employee->getJob()?->getName()}</td>
                      </tr>";
            $counter++;
        }

        $html .= '</table>';

        $dompdf->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filePath = sys_get_temp_dir() . '/' . $fileName . '.pdf';
        file_put_contents($filePath, $dompdf->output());

        return $filePath;
    }
}