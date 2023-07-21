<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;

class UsersFixtures extends Fixture
{
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
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
