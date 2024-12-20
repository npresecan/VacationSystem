<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

#[AsCommand(
    name: 'app:add-vacation-days',
    description: 'Add 20 vacation days to all employees for the new year.',
)]
class AddVacationDaysCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Adding 20 vacation days to all employees...');
        
        $employees = $this->entityManager->getRepository(Employee::class)->findAll();
        
        foreach ($employees as $employee) {
            $output->writeln('Current vacation days for ' . $employee->getFirstName() . ': ' . $employee->getVacationDays());
            
            $newVacationDays = $employee->getVacationDays() + 20; 
            $employee->setVacationDays($newVacationDays); 
            
            $output->writeln('New vacation days for ' . $employee->getFirstName() . ': ' . $newVacationDays);

            $this->entityManager->persist($employee);
        }

        $this->entityManager->flush();

        $output->writeln('Vacation days added successfully.');

        return Command::SUCCESS;
    }
}

