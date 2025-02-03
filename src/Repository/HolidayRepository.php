<?php

namespace App\Repository;

use App\Entity\Holiday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Holiday>
 */
class HolidayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holiday::class);
    }

    //    /**
    //     * @return Holiday[] Returns an array of Holiday objects
    //     */

    public function getHolidayDates(): array
    {
        $holidays = $this->findAll(); 
        $holidayDates = [];

        foreach ($holidays as $holiday) {
            $holidayDates[] = $holiday->getDate()->format('Y-m-d');
        }

        return $holidayDates;
    }
}
