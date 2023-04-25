<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $marketing = new User();
        $marketing->setEmail('marketing@marketing.com');
        $marketing->setFirstname('Marketing');
        $marketing->setLastname('Marketing');
        $password = password_hash('marketing', PASSWORD_BCRYPT);
        $marketing->setPassword($password);
        $marketing->setRoles(['ROLE_MARKETING']);
        $manager->persist($marketing);
        $manager->flush();
    }
}
