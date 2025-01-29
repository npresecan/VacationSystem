<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Employee;

class FunctionTwig extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('filter_employees', [$this, 'filterEmployees']),
        ];
    }

    public function filterEmployees(array $employees, ?string $query): array
    {
        if (empty($query)) {
            return $employees; 
        }

        return array_filter($employees, function (Employee $employee) use ($query) {
            $query = strtolower($query);

            return str_contains(strtolower($employee->getFirstName()), $query) || str_contains(strtolower($employee->getLastName()), $query);
        });
    }
}