<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use App\Entity\Team;
use App\Entity\Gender;
use App\Entity\Customer;
use App\Entity\CompanyType;
use App\Entity\Company;

class AppFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {
        $this->truncate($manager);
        $this->genderFixtures($manager);
        $this->teamFixtures($manager);
        $this->companyTypeFixtures($manager);
        $this->companyFixtures($manager);
        $this->customersFixtures($manager);
        $this->usersFixtures($manager);
    }

    protected function genderFixtures($manager): void 
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

    protected function teamFixtures($manager): void
    {
        $team = new Team;
        $team->setEmail('xavier.tezza@comnstay.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'Legion@2023'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender($this->getReferencedObject(Gender::class, 2, $manager));
        $team->setFirstname('Xavier');
        $team->setLastname('TEZZA');
        $team->setPhone('06 16 94 06 53');
        $manager->persist($team);

        $team = new Team;
        $team->setEmail('angie.racca.munoz@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($team, 'Legion@2023');
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender($this->getReferencedObject(Gender::class, 1, $manager));
        $team->setFirstname('Angie');
        $team->setLastname('RACCA');
        $manager->persist($team);

        $manager->flush();
    }

    protected function companyTypeFixtures($manager): void
    {
        $companyType1 = new CompanyType;
        $companyType1->setName('Association');
        $manager->persist($companyType1);

        $companyType2 = new CompanyType;
        $companyType2->setName('Eleveur');
        $manager->persist($companyType2);

        $manager->flush();
    }

    protected function companyFixtures($manager): void
    {
        for($i=1; $i<=3; $i++) {
            $company = new Company;
            $company->setName($this->faker->company());
            $company->setEmail('company' . $i . '@comnstay.fr');
            $company->setAddress($this->faker->streetAddress());
            $company->setZipCode($this->faker->postcode());
            $company->setTown($this->faker->city());
            $company->setStatus(false);
            $company->setFkCompanyType($this->getRandomReference('App\Entity\CompanyType', $manager));
            $company->setLogo('https://loremflickr.com/640/480/pets');
            $manager->persist($company);
        }
        $manager->flush();
    }

    protected function customersFixtures($manager): void
    {
        for($i=1; $i<=5; $i++) {
            $customer = new Customer;
            $customer->setEmail('customer' . $i . '@comnstay.fr');
            $gender = ($i++ & 1) ? 1 : 2;
            $customer->setFkGender($this->getReferencedObject(Gender::class, $gender, $manager));
            $customer->setFirstname($this->faker->firstname());
            $customer->setLastname($this->faker->lastname());
            $customer->setStatus(($i++ & 1) ? true : false);
            $customer->setFkCompany($this->getRandomReference('App\Entity\Company', $manager));
            $hashedPassword = $this->passwordHasher->hashPassword($customer, 'Legion@2023');
            $customer->setPassword($hashedPassword);
            $role = ($i++ & 1) ? 'ROLE_ADMIN_CUSTOMER' : 'ROLE_CUSTOMER';
            $customer->setRoles([$role]);
            $manager->persist($customer);
        }
        $manager->flush();
    }

    protected function usersFixtures($manager): void
    {
        for($i=1; $i<=5; $i++) {
            $user = new User;
            $user->setEmail('user' . $i . '@comnstay.fr');
            $gender = ($i++ & 1) ? 1 : 2;
            $user->setFkGender($this->getReferencedObject(Gender::class, $gender, $manager));
            $user->setFirstname($this->faker->firstname());
            $user->setLastname($this->faker->lastname());
            $hashedPassword = $this->passwordHasher->hashPassword($user, 'Legion@2023');
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_IDENTIFIED']);
            $manager->persist($user);
        }
        $manager->flush();
    }

    protected function getReferencedObject(string $className, int $id, object $manager) {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager) {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }

    protected function truncate($manager) : void
    {
        /** @var Connection db */
        $db = $manager->getConnection();

        // start new transaction
        $db->beginTransaction();

        $sql = '
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE company_type;
            TRUNCATE team; 
            TRUNCATE user;
            TRUNCATE customer;
            TRUNCATE gender;
            TRUNCATE company; 
            SET FOREIGN_KEY_CHECKS=1;
            ';
        $db->prepare($sql);
        $db->executeQuery($sql);

        $db->commit();
        $db->beginTransaction();
    }
}
