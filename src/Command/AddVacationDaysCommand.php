<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:add-vacation-days',
    description: 'Add vacation days to all employees for the new year.',
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
        $this
            ->addArgument('days', InputArgument::REQUIRED, 'Number of vacation days to add to each employee');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $daysToAdd = (int) $input->getArgument('days');

        if ($daysToAdd <= 0) {
            $io->error('The number of days must be a positive integer.');
            return Command::FAILURE;
        }

        $io->info("Adding $daysToAdd vacation days to all employees...");

        $connection = $this->entityManager->getConnection();
        $sql = 'UPDATE employee SET vacation_days = vacation_days + :days';
        $stmt = $connection->prepare($sql);
        $stmt->executeQuery(['days' => $daysToAdd]);

        $io->success('Vacation days added successfully to all employees.');

        return Command::SUCCESS;
    }
}