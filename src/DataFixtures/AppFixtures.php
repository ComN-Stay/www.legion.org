<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;
use App\Entity\Team;
use App\Entity\Gender;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        /** @var Connection db */
        $db = $manager->getConnection();

        // start new transaction
        $db->beginTransaction();

        $sql = 'TRUNCATE gender; TRUNCATE team; TRUNCATE user';
        $db->prepare($sql);
        $db->executeQuery($sql);

        // commit and re-start new transaction
        $db->commit();
        // work-around bug "There is no active transaction" in data-fixtures in php8
        $db->beginTransaction();
    }

    public function getSqlResult()
    {   
        $sql = 'TRUNCATE gender; TRUNCATE team; TRUNCATE user';

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->executeQuery()->fetchAllAssociative();
    }   

    public static function getReferencedObject($className, $id, $manager) {
        return $manager->find($className, $id);
    }
}
