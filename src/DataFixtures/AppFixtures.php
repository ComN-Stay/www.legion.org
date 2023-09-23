<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tags;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Gender;
use App\Entity\Medias;
use App\Entity\Status;
use App\Entity\Adverts;
use App\Entity\Company;
use App\Entity\Articles;
use App\Entity\Consents;
use App\Entity\PetsType;
use App\Entity\Petitions;
use App\Entity\PagesTypes;
use App\Entity\Statistics;
use App\Entity\CompanyType;
use App\Entity\ArticlesMedias;
use App\Repository\PagesRepository;
use App\Repository\StatusRepository;
use App\Repository\ArticlesRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\PagesTypesRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;
    private $slugger;
    private $output;
    private $kernel;
    private $articlesRepository;
    private $pagesRepository;
    private $statusRepository;
    private $pagesTypesRepository;
    
    public function __construct(
        UserPasswordHasherInterface $passwordHasher, 
        SluggerInterface $sluggerInterface, 
        KernelInterface $kernel, 
        ArticlesRepository $articlesRepository,
        PagesRepository $pagesRepository,
        StatusRepository $statusRepository,
        PagesTypesRepository $pagesTypesRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
        $this->slugger = $sluggerInterface;
        $this->kernel = $kernel;
        $this->articlesRepository = $articlesRepository;
        $this->pagesRepository = $pagesRepository;
        $this->statusRepository = $statusRepository;
        $this->pagesTypesRepository = $pagesTypesRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $output = new ConsoleOutput();
        $this->output = $output;
        if($this->purgeDatabase($manager) == true) {
            $this->output->writeln('<info>Database reloaded.</info>');
            $this->statusFixtures($manager);
            $this->genderFixtures($manager);
            $this->teamFixtures($manager);
            $this->companyTypeFixtures($manager);
            $this->companyFixtures($manager, 30);
            $this->PagesTypeFixtures($manager);
            $this->PagesFixtures($manager);
            $this->usersFixtures($manager, 100);
            $this->petsTypeFixtures($manager);
            $this->adsFixtures($manager, 500);
            $this->mediasFixtures($manager, 2000);
            $this->statisticsFixtures($manager);
            $this->petitionsFixtures($manager);
            $this->tagsFixtures($manager);
            $this->transactionalVarsFixtures($manager);
            $this->transactionalFixtures($manager);
            $this->ArticlesFixtures($manager, 200);
            $this->ArticleMediasFixtures($manager);
            $this->output->writeln('<info>Done ! fixtures loaded.</info>');
        }
    }

    protected function statusFixtures($manager): void 
    {
        $this->output->writeln('<info>Loading Status fixtures ...</info>');

        $statuses = ['En cours de rédaction', 'En attente de publication', 'En ligne', 'refusé', 'Archivé'];
        $i = 1;
        foreach($statuses as $status) {
            $st[$i] = new Status;
            $st[$i]->setName($status);
            $manager->persist($st[$i]);
            $i++;
        }
        $manager->flush();
        $this->output->writeln('<info>Status fixtures loaded</info>');
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

        $team = new Team;
        $team->setEmail('contact.site.com.on@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($team, 'Legion@2023');
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_SUPER_ADMIN']);
        $team->setFkGender($this->getReferencedObject(Gender::class, 1, $manager));
        $team->setFirstname('Muriel');
        $team->setLastname('THOMAS');
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
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
            $company[$i] = new Company;
            $company[$i]->setName($this->faker->company());
            $company[$i]->setEmail('company' . $i . '@comnstay.fr');
            $company[$i]->setAddress($this->faker->streetAddress());
            $company[$i]->setZipCode(str_replace(' ', '', $this->faker->postcode()));
            $company[$i]->setTown($this->faker->city());
            $company[$i]->setStatus(($i % 2 == 0) ? false : true);
            $company[$i]->setFkCompanyType($this->getRandomReference('App\Entity\CompanyType', $manager));
            $company[$i]->setLogo('logo' . rand(1, 10) . '.jpg');
            $company[$i]->setShortDescription($this->faker->paragraphs(1, true));
            $company[$i]->setDescription($this->faker->paragraphs(rand(2, 4), true));
            $company[$i]->setLatitude($this->float_rand('42.37', '51.06'));
            $company[$i]->setLongitude($this->float_rand('-4.73', '8.30'));
            $manager->persist($company[$i]);
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
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
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
            $user[$i] = new User;
            $user[$i]->setEmail('user' . $i . '@legion.local');
            $gender = ($i % 2 == 0) ? 1 : 2;
            $user[$i]->setFkGender($this->getReferencedObject(Gender::class, $gender, $manager));
            $user[$i]->setFirstname($this->faker->firstname());
            $user[$i]->setLastname($this->faker->lastname());
            $hashedPassword = $this->passwordHasher->hashPassword($user[$i], 'Legion@2023');
            $user[$i]->setPassword($hashedPassword);
            $roles = ['ROLE_IDENTIFIED', 'ROLE_CUSTOMER', 'ROLE_ADMIN_CUSTOMER'];
            $role = array_rand(array_flip($roles), 1);
            $user[$i]->setRoles([$role]);
            $user[$i]->setIsCompanyAdmin(($role == 'ROLE_ADMIN_CUSTOMER') ? 1 : 0);
            if($role != 'ROLE_IDENTIFIED') {
                $company = $this->getRandomReference('App\Entity\Company', $manager);
                $user[$i]->setFkCompany($company);
            }
            $user[$i]->setToken(bin2hex(random_bytes(60)));
            $manager->persist($user[$i]);
            $id = $user[$i]->getId();
            
            $status = $this->statusRepository->find(3);
            $pageType = $this->pagesTypesRepository->find(1);
            $consentType = $this->pagesRepository->findOneBy(['fk_type' => $pageType, 'fk_status' => $status]);
            $consents = new Consents;
            $consents->setFkUser($user[$i]);
            $consents->setFkType($consentType->getFkType());
            $consents->setDateAdd(new \DateTime(date('Y-m-d H:i:s')));
            $consents->setVersion($consentType->getVersion());
            $consents->setVersionDate($consentType->getdateAdd());
            
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
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
        $progressBar->setMessage("Starting...", 'status');
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
            $ad[$i]->setFkStatus($this->getRandomReference('App\Entity\Status', $manager));
            $ad[$i]->setLof(false);
            $ad[$i]->setVisits(rand(4, 1521));
            $ad[$i]->setDateAdd(new \DateTime(date('Y-m-d H:i:s')));
            $price =  ($i % 3 == 0) ? 0 : rand(50, 500);
            $ad[$i]->setPrice($price);
            $manager->persist($ad[$i]);
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
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
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
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
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();

        $i = 1;
        $begin = new \DateTime(date('Y-m-d'));
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
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
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
            'https://chng.it/sdrm9cPdFK',
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

    public function transactionalVarsFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Transactionals vars fixtures ...</info>');

        $db = $manager->getConnection();
        $db->beginTransaction();

        $sql = "INSERT INTO `transactional_vars` (`id`, `var_table`, `var_field`, `description`) VALUES
        (1, 'team', 'email', 'Email de l\'administrateur'),
        (2, 'team', 'lastname', 'Nom de l\'administrateur'),
        (3, 'user', 'email', 'Email de l\'utilisateur'),
        (4, 'user', 'firstname', 'Prénom de l\'utilisateur'),
        (5, 'user', 'lastname', 'Nom de l\'utilisateur'),
        (6, 'petitions', 'title', 'Titre de la pétition'),
        (7, 'petitions', 'link', 'Lien de la pétition'),
        (8, 'company', 'name', 'Nom de l\'entité'),
        (9, 'company', 'email', 'Email de l\'entité'),
        (10, 'articles', 'title', 'Titre de l\'article'),
        (11, 'adverts', 'title', 'Titre de l\'annonce');";
        
        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        $this->output->writeln('<info>Transactionals vars fixtures loaded</info>');
    }

    public function transactionalFixtures($manager): void
    {
        $this->output->writeln('<info>Loading Transactionals fixtures ...</info>');

        $db = $manager->getConnection();
        $db->beginTransaction();

        $sql = "INSERT INTO `transactional` (`id`, `description`, `template`, `subject`, `content`) VALUES
        (1, 'Mail envoyé lors de l\'inscription d\'un internaute', 'welcome_user', 'Bienvenue sur Légion', 'Bonjour {{ user.firstname }} {{ user.lastname }},\r\nBienvenue sur L&eacute;gion !'),
        (2, 'Email envoyé lors de la création d\'un compte admin', 'admin_password_create', 'Création de votre compte administrateur sur Légion', 'Bonjour {{ team.firstname }},\r\nTon compte administrateur vient d\'&ecirc;tre cr&eacute;&eacute; sur L&eacute;gion !\r\nIl ne te reste plus qu\'&agrave; suivre ce lien pour cr&eacute;er ton mot de passe :\r\n<a href=\"{{%20app.request.schemeAndHttpHost%20}}/reset/{{%20resetToken%20}}\">Cr&eacute;er mon mot de passe</a>\r\n<div>\r\n<div>Attention, ce lien n\'est valide que durant 1 heure</div>\r\n</div>'),
        (3, 'Mail envoyé pour la réinitialisation du mot de passe (users et admins)', 'reset_password', 'Réinitialisation de votre mot de passe', 'Bonjour,\r\nvous avez demand&eacute; la r&eacute;initialisation de votre mot de passe.\r\nSuivez le len ci-dessous afin de le r&eacute;initialiser\r\n<a href=\"{{%20app.request.schemeAndHttpHost%20}}/reset-password/reset/{{%20resetToken.token%20}}\">R&eacute;initialiser mon mot de passe</a>\r\nAttention, ce lien n\'est valide que durant 1 heure'),
        (4, 'Mail envoyé à l\'internaute lors de la création du compte d\'une structure', 'welcome_company', 'Bienvenue sur Légion !', 'Bonjour {{ user.firstname }},\r\nVotre compte a &eacute;t&eacute; cr&eacute;&eacute; sur L&eacute;gion, vous pouvez d&eacute;sormais vous connecter et commencer &agrave; g&eacute;rer votre structure \"{{ company.name }}\" !\r\nCelle ci n\'est pas encore active, vous recevrez un email lors de son activation par un administrateur.'),
        (5, 'Mail envoyé à l\'administrateur principal d\'une structure lors de son activation', 'company_activation', 'Activation de votre compte', 'Bonjour {{ user.firstname }},\r\nNous avons le plaisir de vous informer que le compte de votre structure {{ company.name }} vient d\'&ecirc;tre activ&eacute; !\r\nLes internautes ont acc&egrave;s &agrave; sa fiche et peuvent voir vos annonces, p&eacute;titions et autres articles !'),
        (6, 'Mail envoyé lors de l\'a mise en ligne d\'une annonce', 'active_advert', 'Activation de votre annonce', 'Bonjour,\r\nG&eacute;nial !!!\r\nVotre annonce {{ adverts.title }} est en ligne !'),
        (7, 'Mail envoyé lors du refus de mise en ligne d\'une annonce', 'advert_rejected', 'Refus de votre annonce', 'Bonjour,\r\nOUPSSSSS ...\r\nIl semblerai que votre anonce {{ adverts.title }} ne correspondent pas aux standards de la communaut&eacute;.\r\nNous avons donc d&eacute;cid&eacute; de ne pas la mettre en ligne telle qu\'elle, vous pouvez la modifier et la proposer de nouveau &agrave; l\'&eacute;valuation'),
        (8, 'Mail envoyé lors du refus de mise en ligne d\'un article', 'article_rejected', 'Refus de votre article', 'Bonjour,\r\nOUPSSSSS ...\r\nIl semblerai que votre article {{ articles.title }} ne correspondent pas aux standards de la communaut&eacute;.\r\nNous avons donc d&eacute;cid&eacute; de ne pas la mettre en ligne telle qu\'elle, vous pouvez la modifier et la proposer de nouveau &agrave; l\'&eacute;valuation'),
        (9, 'Mail envoyé lors de la suppression d\'une annonce', 'delete_advert', 'Suppression de votre annonce', 'Bonjour,\r\nVotre annonce {{ adverts.title }} a bien &eacute;t&eacute; supprim&eacute;e');
        ";
        
        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        $this->output->writeln('<info>Transactionals fixtures loaded</info>');
    }

    public function ArticlesFixtures($manager, $nb)
    {
        $this->output->writeln('<info>Loading Articles fixtures ...</info>');
        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();

        for($i=1; $i<=$nb; $i++) {
            $article[$i] = new Articles;
            $title = $this->faker->catchPhrase();
            $article[$i]->setTitle($title);
            $article[$i]->setShortDescription($this->faker->paragraphs(1, true));
            $article[$i]->setContent($this->faker->paragraphs(rand(2, 4), true));
            $article[$i]->setDateAdd(new \DateTime(date('Y-m-d')));
            $article[$i]->setVisits(rand(3,658));
            $article[$i]->setFkStatus($this->getRandomReference('App\Entity\Status', $manager));
            $article[$i]->setSlug($this->slugger->slug($title));
            $article[$i]->setMetaName($title);
            $article[$i]->setMetaDescription($this->faker->catchPhrase());
            $article[$i]->setLogo('logo' . rand(1, 10) . '.jpg');
            for($j=0;$j<=4;$j++) {
                $article[$i]->addTag($this->getRandomReference('App\Entity\Tags', $manager));
            }
            $manager->persist($article[$i]);
            if(($i % 3 == 0)) {
                $article[$i]->setFkUser($this->getRandomReference('App\Entity\User', $manager));
            } else {
                $article[$i]->setFkTeam($this->getRandomReference('App\Entity\Team', $manager));
            }
            $manager->persist($article[$i]);
            $progressBar->setMessage("Job in progress...", 'status');
            $progressBar->advance();
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Articles fixtures loaded</info>');
    }

    public function ArticleMediasFixtures($manager)
    {
        $articles = $this->articlesRepository->findAll();
        $nb = count($articles);
        $i = 1;
        $this->output->writeln('<info>Loading Articles medias fixtures ...</info>');
        $progressBar = new ProgressBar($this->output, $nb);
        $progressBar->setFormat("<fg=white;bg=cyan> %status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-21s% %memory:21s%");
        $progressBar->setBarCharacter('<fg=green>⚬</>');
        $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $progressBar->setProgressCharacter("<fg=green>➤</>");
        $progressBar->setMessage("Starting...", 'status');
        $progressBar->start();
        foreach($articles as $article) {
            for($j=1; $j<=4; $j++) {
                $media[$i][$j] = new ArticlesMedias;
                $media[$i][$j]->setTitle($this->faker->catchPhrase());
                $media[$i][$j]->setFile('logo' . rand(1, 10) . '.jpg');
                $media[$i][$j]->setFkArticle($article);
                $manager->persist($media[$i][$j]);
                $progressBar->setMessage("Job in progress...", 'status');
                $progressBar->advance();
            }
        }
        $progressBar->setMessage("Jobs Done !", 'status');
        $manager->flush();
        $progressBar->finish();
        $this->output->writeln('');
        $this->output->writeln('<info>Articles medias fixtures loaded</info>');
    }

    public function PagesTypeFixtures($manager)
    {
        $sql = "INSERT INTO `pages_types` (`id`, `name`, `type`, `has_version`) VALUES
        (1, 'Conditions générales d\'utilisation utilisateur', 'cgu_user', 1),
        (2, 'Conditions générales d\'utilisation company', 'cgu_company', 1),
        (3, 'Conditions générales de vente', 'cgv', 1),
        (4, 'Newsletter', 'news', 0),
        (5, 'Politique de protection des données', 'rgpd', 1);";
    
        $this->output->writeln('<info>Loading PagesType fixtures ...</info>');
    
        $db = $manager->getConnection();
        $db->beginTransaction();
        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        $this->output->writeln('<info>PagesType fixtures loaded</info>');
    }

    public function PagesFixtures($manager)
    {
        $sql = "INSERT INTO `pages` (`id`, `title`, `description`, `slug`, `meta_title`, `meta_description`, `meta_keywords`, `date_add`, `fk_type_id`, `version`, `fk_status_id`) VALUES
        (2, 'Conditions générales d\'utilisation', '<p>Conditions g&eacute;n&eacute;rales d\'utilisation</p>', 'Conditions-generales-d-utilisation', 'Conditions générales d\'utilisation', 'Conditions générales d\'utilisation', 'Conditions générales d\'utilisation', '2023-09-21', 1, 1, 5),
        (3, 'Conditions générales d\'utilisation V2', '<p>Conditions g&eacute;n&eacute;rales d\'utilisation</p>', 'Conditions-generales-d-utilisation-V2', 'Conditions générales d\'utilisation', 'Conditions générales d\'utilisation', 'Conditions générales d\'utilisation', '2023-09-22', 1, 2, 5),
        (4, 'Conditions générales d\'utilisation V3', '<p>Conditions g&eacute;n&eacute;rales d\'utilisation</p>', 'Conditions-generales-d-utilisation-V3', NULL, NULL, NULL, '2023-09-22', 1, 3, 3);";

        $this->output->writeln('<info>Loading Pages fixtures ...</info>');

        $db = $manager->getConnection();
        $db->beginTransaction();
        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        $this->output->writeln('<info>Pages fixtures loaded</info>');
    }

    protected function getReferencedObject(string $className, int $id, object $manager) {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager) {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }

    protected function purgeDatabase($manager)
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
            DROP TABLE IF EXISTS `transactional_vars`;
            DROP TABLE IF EXISTS `transactional`; 
            DROP TABLE IF EXISTS `user`; 
            DROP TABLE IF EXISTS `visitors`;
            DROP TABLE IF EXISTS `articles`;
            DROP TABLE IF EXISTS `articles_medias`;
            DROP TABLE IF EXISTS `articles_tags`;
            DROP TABLE IF EXISTS `status`;
            DROP TABLE IF EXISTS `pages`;
            DROP TABLE IF EXISTS `pages_types`;
            DROP TABLE IF EXISTS `consents`;
            SET FOREIGN_KEY_CHECKS=1;
            ';

        $db->prepare($sql);
        $db->executeQuery($sql);
        $db->commit();
        $db->beginTransaction();
        
        $this->output->writeln('<info>Purge OK</info>');

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate'
        ]);

        //$output = new NullOutput();
        $application->run($input, $this->output);
        return true;
    }

    private function float_rand($min, $max)
    {
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        $randomfloat = round($randomfloat, 5);
    
        return $randomfloat;
    }

}
