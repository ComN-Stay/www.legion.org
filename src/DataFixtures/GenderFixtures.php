<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Gender;

class GenderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $gender1 = new Gender;
        $gender1->setName('Madame');
        $gender1->setShortName('Mme');
        $manager->persist($gender1);

        $gender2 = new Gender;
        $gender2->setName('Monsieur');
        $gender2->setShortName('M.');
        $manager->persist($gender2);

        $gender3 = new Gender;
        $gender3->setName('Sexe neutre');
        $gender3->setShortName('');
        $manager->persist($gender3);

        $manager->flush();
    }
}
