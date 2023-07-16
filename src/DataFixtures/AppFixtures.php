<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use App\Entity\Team;

class AppFixtures extends Fixture
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
        $manager->persist($team);

        $team = new Team;
        $team->setEmail('angie.racca.munoz@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'Legion@2023'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($team);

        $manager->flush();

        // create fake customers
        for($i=1; $i<=5; $i++) {
            $user = new User;
            $user->setEmail('user' . $i . '@comnstay.fr');
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'Legion@2023'
            );
            $user->setPassword($hashedPassword);
            $role = ($i == 1) ? 'ROLE_ADMIN_CUSTOMER' : 'ROLE_CUSTOMER';
            $user->setRoles([$role]);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
