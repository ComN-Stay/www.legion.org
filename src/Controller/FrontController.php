<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VisitorsRepository;
use App\Repository\StatisticsRepository;
use App\Entity\Visitors;
use App\Entity\Statistics;

class FrontController extends AbstractController
{
    private $statisticsRepository;
    private $visitorsRepository;
    private $em;

    public function __construct(
        StatisticsRepository $statisticsRepository, 
        VisitorsRepository $visitorsRepository, 
        EntityManagerInterface $em
    )
    {
        $this->statisticsRepository = $statisticsRepository;
        $this->visitorsRepository = $visitorsRepository;
        $this->em = $em;
        $this->statistics();
    }

    public function statistics()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $today = $date = new \DateTime(date('Y-m-d'));
        $date->format('Y-m-d');
        $visitor = $this->visitorsRepository->findOneBy(['ip' => $ip]);
        $statLine = $this->statisticsRepository->findOneBy(['day' => $today]);
        $new = 0;
        if(null === $statLine) {
            $statLine = new Statistics;
            $statLine->setDay($today);
            $statLine->setVisits(1);
            $this->em->persist($statLine);
            $this->em->flush();
            $new = 1;
        }
        if(null !== $visitor) {
            if($visitor->getDateAdd() != $today) {
                $visitor->setDateAdd($today);
                $this->em->persist($visitor);
                $this->em->flush();
                if($new == 0) {
                    $statLine->setVisits($statLine->getVisits() + 1);
                    $this->em->persist($statLine);
                    $this->em->flush();
                }
            }
        } else {
            $visitor = new Visitors;
            $visitor->setIp($ip);
            $visitor->setDateAdd($today);
            $this->em->persist($visitor);
            $this->em->flush();
            if($new == 0) {
                $statLine->setVisits($statLine->getVisits() + 1);
                $this->em->persist($statLine);
                $this->em->flush();
            }
        }
        
    }
}
