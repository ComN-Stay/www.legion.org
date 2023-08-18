<?php

namespace App\DataFixtures;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use App\Entity\Team;
use App\Entity\Tags;
use App\Entity\Statistics;
use App\Entity\PetsType;
use App\Entity\Petitions;
use App\Entity\Medias;
use App\Entity\Gender;
use App\Entity\CompanyType;
use App\Entity\Company;
use App\Entity\Adverts;

class AppFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;
    private $slugger;
    private $output;
    private $params;
    private $kernel;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher, SluggerInterface $sluggerInterface, ParameterBagInterface $params, KernelInterface $kernel)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
        $this->slugger = $sluggerInterface;
        $this->params = $params;
        $this->kernel = $kernel;
    }

    public function load(ObjectManager $manager): void
    {
        $output = new ConsoleOutput();
        $this->output = $output;
        $this->purgeDatabase($manager);
        $this->genderFixtures($manager);
        $this->teamFixtures($manager);
        $this->companyTypeFixtures($manager);
        $this->companyFixtures($manager, 30);
        $this->usersFixtures($manager, 300);
        $this->petsTypeFixtures($manager);
        $this->adsFixtures($manager, 500);
        $this->mediasFixtures($manager, 2000);
        $this->statisticsFixtures($manager);
        $this->petitionsFixtures($manager);
        $this->tagsFixtures($manager);
        $this->transactionalFixtures($manager);
    }

    protected function genderFixtures($manager): void 
    {
        $this->output->writeln('<info>Loading Gender fixtures ...</info>');
        
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
        $this->output->writeln('<info>Gender fixtures loaded</info>');
    }

    protected function teamFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Team fixtures ...</info>');

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
        $this->output->writeln('<info>Team fixtures loaded</info>');
    }

    protected function companyTypeFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Company Type fixtures ...</info>');

        $companyType1 = new CompanyType;
        $companyType1->setName('Association');
        $manager->persist($companyType1);

        $companyType2 = new CompanyType;
        $companyType2->setName('Eleveur');
        $manager->persist($companyType2);

        $manager->flush();
        $this->output->writeln('<info>Company Type fixtures loaded</info>');
    }

    protected function companyFixtures($manager, $nb): void
    {
        $this->output->writeln('<info>Loading Company fixtures ...</info>');

        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        
        $progressBar->setRedrawFrequency(10);
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
            $company[$i] = new Company;
            $company[$i]->setName($this->faker->company());
            $company[$i]->setEmail('company' . $i . '@comnstay.fr');
            $company[$i]->setAddress($this->faker->streetAddress());
            $company[$i]->setZipCode($this->faker->postcode());
            $company[$i]->setTown($this->faker->city());
            $company[$i]->setStatus(false);
            $company[$i]->setFkCompanyType($this->getRandomReference('App\Entity\CompanyType', $manager));
            $company[$i]->setLogo('logo' . rand(1, 10) . '.jpg');
            $manager->persist($company[$i]);
            if ($i == round(($i/$nb)*33)) {
                $progressBar->setMessage("All right :)", 'status');
            } elseif($i == round(($i/$nb)*66)) {
                $progressBar->setMessage("Almost there...", 'status');
            }
            $progressBar->advance();
            usleep(1000);
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Company fixtures loaded</info>');
    }

    protected function usersFixtures($manager, $nb): void
    {
        $this->output->writeln('<info>Loading Users fixtures ...</info>');
        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
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
            if ($i == round(($i/$nb)*33)) {
                $progressBar->setMessage("All right :)", 'status');
            } elseif($i == round(($i/$nb)*66)) {
                $progressBar->setMessage("Almost there...", 'status');
            }
            $progressBar->advance();
            usleep(1000);
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Users fixtures loaded</info>');
    }

    protected function petsTypeFixtures($manager)
    {
        $this->output->writeln('<info>Loading Pets Type fixtures ...</info>');

        $types = ['Chats', 'Chiens', 'Oiseaux', 'Tortues', 'NAC'];
        $i = 1;
        foreach($types as $type) {
            $pet[$i] = new PetsType;
            $pet[$i]->setName($type);
            $manager->persist($pet[$i]);
        }
        $manager->flush();
        $this->output->writeln('<info>Pets fixtures loaded</info>');
    }

    protected function adsFixtures($manager, $nb): void
    {
        $this->output->writeln('<info>Loading Adverts fixtures ...</info>');

        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
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
            $ad[$i]->setVisits(rand(4, 1521));
            $manager->persist($ad[$i]);
            if ($i == round(($i/$nb)*33)) {
                $progressBar->setMessage("All right :)", 'status');
            } elseif($i == round(($i/$nb)*66)) {
                $progressBar->setMessage("Almost there...", 'status');
            }
            $progressBar->advance();
            usleep(1000);
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Adverts fixtures loaded</info>');
    }

    protected function mediasFixtures($manager, $nb): void
    {
        $this->output->writeln('<info>Loading Medias fixtures ...</info>');
        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
            $media[$i] = new Medias;
            $media[$i]->setTitle($this->faker->catchPhrase());
            $media[$i]->setFilename('logo' . rand(1, 10) . '.jpg');
            $media[$i]->setFkAdvert($this->getRandomReference('App\Entity\Adverts', $manager));
            $manager->persist($media[$i]);
            if ($i == round(($i/$nb)*33)) {
                $progressBar->setMessage("All right :)", 'status');
            } elseif($i == round(($i/$nb)*66)) {
                $progressBar->setMessage("Almost there...", 'status');
            }
            $progressBar->advance();
            usleep(1000);
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Medias fixtures loaded</info>');
    }

    protected function statisticsFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Statistics fixtures ...</info>');

        $progressBar = new ProgressBar($this->output, 600);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->start();

        $i = 1;
        $begin = new \DateTime('2012-01-01');
        $begin->modify('-600 day');
        $end = new \DateTime(date('Y-m-d'));
        $end->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval, $end);
        foreach($daterange as $date){
            $stat[$i] = new Statistics;
            $stat[$i]->setDay($date);
            $stat[$i]->setVisits(rand(2, 457));
            $manager->persist($stat[$i]);
            if ($i == round(($i/600)*33)) {
                $progressBar->setMessage("All right :)", 'status');
            } elseif($i == round(($i/600)*66)) {
                $progressBar->setMessage("Almost there...", 'status');
            }
            $progressBar->advance();
            usleep(1000);
            $i++;
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Statistics fixtures loaded</info>');
    }

    protected function petitionsFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Petitions fixtures ...</info>');

        $links = [
            'https://chng.it/WsyDDYK9L6',
            'https://chng.it/swGpbcXGQs',
            'https://chng.it/hdp8dV5F2p',
            'https://chng.it/sdrm9cPdFK'
        ];
        $i = 1;
        foreach($links as $link){
            $petiton[$i] = new Petitions;
            $petiton[$i]->setTitle($this->faker->catchPhrase());
            $petiton[$i]->setDescription($this->faker->paragraphs(rand(2, 4), true));
            $petiton[$i]->setLink($link);
            $petiton[$i]->setDateAdd(new \DateTime(date('Y-m-d')));
            $petiton[$i]->setStatus(($i % 2 == 0) ? false : true);
            $petiton[$i]->setFkUser($this->getRandomReference('App\Entity\User', $manager));
            $manager->persist($petiton[$i]);
            $i++;
        }
        $manager->flush();
        $this->output->writeln('<info>Petitions fixtures loaded</info>');
    }

    public function tagsFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Tags fixtures ...</info>');

        $tags = ['Chats', 'Chiens', 'Oiseaux', 'Tortues', 'NAC', 'Soins', 'Bien-être animal'];
        $i = 1;
        foreach($tags as $tag) {
            $t[$i] = new Tags;
            $t[$i]->setTitle($tag);
            $t[$i]->setSlug($this->slugger->slug($tag));
            $t[$i]->setArticleQte(rand(1, 25));
            $t[$i]->setMetaName($tag);
            $t[$i]->setMetaDescription($this->faker->catchPhrase());
            $t[$i]->setMetaKeyword($this->faker->words(rand(2, 7), true));
            $manager->persist($t[$i]);
            $i++;
        }
        $manager->flush();
        $this->output->writeln('<info>Tags fixtures loaded</info>');
    }

    public function transactionalFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Transactionals fixtures ...</info>');

        $db = $manager->getConnection();
        $db->beginTransaction();

        $sql = "INSERT INTO `transactional` (`id`, `description`, `template`, `subject`, `content`) VALUES
        (1, 'Mail envoyé lors de l\'inscription d\'un internaute', 'welcome_user', 'Bienvenue sur Légion', '<p>Bonjour {{ user.firstname }} {{ user.lastname }},</p>\r\n<p>Bienvenue sur L&eacute;gion !</p>'),
        (2, 'Email envoyé lors de la création d\'un compte admin', 'admin_password_create', 'Création de votre compte administrateur sur Légion', '<p>Bonjour {{ team.firstname }},</p>\r\n<p>Ton compte administrateur vient d\'&ecirc;tre cr&eacute;&eacute; sur L&eacute;gion !</p>\r\n<p>Il ne te reste plus qu\'&agrave; suivre ce lien pour cr&eacute;er ton mot de passe :</p>\r\n<p><a href=\"{{%20app.request.schemeAndHttpHost%20}}/reset/{{%20resetToken%20}}\">Cr&eacute;er mon mot de passe</a></p>\r\n<div>\r\n<div>Attention, ce lien n\'est valide que durant 1 heure</div>\r\n</div>'),
        (3, 'Mail envoyé pour la réinitialisation du mot de passe (users et admins)', 'reset_password', 'Réinitialisation de votre mot de passe', '<p>Bonjour,</p>\r\n<p>vous avez demand&eacute; la r&eacute;initialisation de votre mot de passe.</p>\r\n<p>Suivez le len ci-dessous afin de le r&eacute;initialiser</p>\r\n<p><a href=\"{{%20app.request.schemeAndHttpHost%20}}/reset-password/reset/{{%20resetToken.token%20}}\">R&eacute;initialiser mon mot de passe</a></p>\r\n<p>Attention, ce lien n\'est valide que durant 1 heure</p>');";
        
        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        $this->output->writeln('<info>Transactionals fixtures loaded</info>');
    }

    protected function getReferencedObject(string $className, int $id, object $manager) {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager) {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }

    protected function purgeDatabase($manager) : void
    {   
        $this->output->writeln('<info>Starting purging Database ...</info>');

        $db = $manager->getConnection();
        $db->beginTransaction();

        $sql = '
            SET FOREIGN_KEY_CHECKS = 0;
            DROP TABLE IF EXISTS `adverts`; 
            DROP TABLE IF EXISTS `company`; 
            DROP TABLE IF EXISTS `company_type`; 
            DROP TABLE IF EXISTS `doctrine_migration_versions`; 
            DROP TABLE IF EXISTS `gender`; 
            DROP TABLE IF EXISTS `medias`; 
            DROP TABLE IF EXISTS `messenger_messages`; 
            DROP TABLE IF EXISTS `petitions`; 
            DROP TABLE IF EXISTS `pets_type`; 
            DROP TABLE IF EXISTS `reset_password_request`; 
            DROP TABLE IF EXISTS `statistics`; 
            DROP TABLE IF EXISTS `tags`; 
            DROP TABLE IF EXISTS `team`; 
            DROP TABLE IF EXISTS `transactional`; 
            DROP TABLE IF EXISTS `user`; 
            DROP TABLE IF EXISTS `visitors`;
            SET FOREIGN_KEY_CHECKS=1;
            ';

        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        
        $this->output->writeln('<info>Purge OK</info>');

        $this->output->writeln('<info>You are about to reload the database.</info>');
        $this->output->writeln('<info>Type "enter" to continue !</info>');
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate'
        ]);

        $output = new NullOutput();
        $application->run($input, $output);

    }

}
