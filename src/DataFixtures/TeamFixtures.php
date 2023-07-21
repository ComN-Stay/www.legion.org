<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Team;
use App\DataFixtures\GenderFixtures;

class TeamFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
   
    public function load(ObjectManager $manager): void
    {

        // admin account for admins (Angie and Xavier)
        $team = new Team;
        $team->setEmail('xavier.tezza@comnstay.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'Legion@2023'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender(AppFixtures::getReferencedObject('App\Entity\Gender', 2, $manager));
        $team->setFirstname('Xavier');
        $team->setLastname('TEZZA');
        $team->setPhone('06 16 94 06 53');
        $manager->persist($team);

        $team = new Team;
        $team->setEmail('angie.racca.munoz@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'Legion@2023'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender(AppFixtures::getReferencedObject('App\Entity\Gender', 1, $manager));
        $team->setFirstname('Angie');
        $team->setLastname('RACCA');
        $manager->persist($team);

        $manager->flush();
    }
}
