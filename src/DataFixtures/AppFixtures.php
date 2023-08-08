<?php

namespace App\DataFixtures;

use App\Entity\Adverts;
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
use App\Entity\PetsType;

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
        $this->petsTypeFixtures($manager);
        $this->adsFixtures($manager);
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
        for($i=1; $i<=10; $i++) {
            $company[$i] = new Company;
            $company[$i]->setName($this->faker->company());
            $company[$i]->setEmail('company' . $i . '@comnstay.fr');
            $company[$i]->setAddress($this->faker->streetAddress());
            $company[$i]->setZipCode($this->faker->postcode());
            $company[$i]->setTown($this->faker->city());
            $company[$i]->setStatus(false);
            $company[$i]->setFkCompanyType($this->getRandomReference('App\Entity\CompanyType', $manager));
            $company[$i]->setLogo('https://loremflickr.com/640/480/pets');
            $manager->persist($company[$i]);
        }
        $manager->flush();
    }

    protected function customersFixtures($manager): void
    {
        for($i=1; $i<=50; $i++) {
            $customer[$i] = new Customer;
            $customer[$i]->setEmail('customer' . $i . '@comnstay.fr');
            $gender = ($i % 2 == 0) ? 1 : 2;
            $customer[$i]->setFkGender($this->getReferencedObject(Gender::class, $gender, $manager));
            $customer[$i]->setFirstname($this->faker->firstname());
            $customer[$i]->setLastname($this->faker->lastname());
            $customer[$i]->setStatus(($i % 2 == 0) ? true : false);
            $customer[$i]->setFkCompany($this->getRandomReference('App\Entity\Company', $manager));
            $hashedPassword = $this->passwordHasher->hashPassword($customer[$i], 'Legion@2023');
            $customer[$i]->setPassword($hashedPassword);
            $role = ($i % 2 == 0) ? 'ROLE_ADMIN_CUSTOMER' : 'ROLE_CUSTOMER';
            $customer[$i]->setRoles([$role]);
            $manager->persist($customer[$i]);
        }
        $manager->flush();
    }

    protected function usersFixtures($manager): void
    {
        for($i=1; $i<=100; $i++) {
            $user[$i] = new User;
            $user[$i]->setEmail('user' . $i . '@comnstay.fr');
            $gender = ($i % 2 == 0) ? 1 : 2;
            $user[$i]->setFkGender($this->getReferencedObject(Gender::class, $gender, $manager));
            $user[$i]->setFirstname($this->faker->firstname());
            $user[$i]->setLastname($this->faker->lastname());
            $hashedPassword = $this->passwordHasher->hashPassword($user[$i], 'Legion@2023');
            $user[$i]->setPassword($hashedPassword);
            $user[$i]->setRoles(['ROLE_IDENTIFIED']);
            $manager->persist($user[$i]);
        }
        $manager->flush();
    }

    protected function petsTypeFixtures($manager)
    {
        $types = ['Chats', 'Chiens', 'Oiseaux', 'Tortues', 'NAC'];
        $i = 1;
        foreach($types as $type) {
            $pet[$i] = new PetsType;
            $pet[$i]->setName($type);
            $manager->persist($pet[$i]);
        }
        $manager->flush();
    }

    protected function adsFixtures($manager): void
    {
        for($i=1; $i<=500; $i++) {
            $ad[$i] = new Adverts;
            $ad[$i]->setName($this->faker->firstName());
            $ad[$i]->setTitle($this->faker->catchPhrase());
            $ad[$i]->setShortDescription($this->faker->paragraphs(1, true));
            $ad[$i]->setDescription($this->faker->paragraphs(rand(2, 4), true));
            $ad[$i]->setFkPetsType($this->getRandomReference('App\Entity\PetsType', $manager));
            $company = $this->getRandomReference('App\Entity\Company', $manager);
            $ad[$i]->setFkCompany($company);
            $ad[$i]->setIsPro(($company->getFkCompanyType()->getId() == 2) ? true : false);
            $ad[$i]->setIdentified(($i % 3 == 0) ? false : true);
            $ad[$i]->setVaccinated(($i % 3 == 0) ? false : true);
            $ad[$i]->setStatus(($i % 3 == 0) ? true : false);
            $ad[$i]->setLof(false);
            $manager->persist($ad[$i]);
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
            TRUNCATE pets_type
            TRUNCATE adverts
            SET FOREIGN_KEY_CHECKS=1;
            ';
        $db->prepare($sql);
        $db->executeQuery($sql);

        $db->commit();
        $db->beginTransaction();
    }
}
