<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\CompanyType;

class CompanyTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $companyType1 = new CompanyType;
        $companyType1->setName('Association');
        $manager->persist($companyType1);

        $companyType2 = new CompanyType;
        $companyType2->setName('Eleveur');
        $manager->persist($companyType2);

        $manager->flush();
    }
}
