<?php

namespace App\DataFixtures;

use App\Entity\Level;
use App\Entity\Status;
use App\Entity\UserAccount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $urgencyLevel = new Level();
        $urgencyLevel->setName("Urgent")->setColor("#eb2f06");
        $manager->persist($urgencyLevel);

        $mediumLevel = new Level();
        $mediumLevel->setName("Medium")->setColor("#f6b93b");
        $manager->persist($mediumLevel);

        $lowLevel = new Level();
        $lowLevel->setName("Low")->setColor("#4a69bd");
        $manager->persist($lowLevel);

        $newStatus = new Status();
        $newStatus->setName("New")->setColor("#eb2f06");
        $manager->persist($newStatus);

        $openStatus = new Status();
        $openStatus->setName("Open")->setColor("#f6b93b");
        $manager->persist($openStatus);

        $closedStatus = new Status();
        $closedStatus->setName("Closed")->setColor("#78e08f");
        $manager->persist($closedStatus);

        $admin = new UserAccount();
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setEmail("admin@test.fr");
        $admin->setPassword($this->passwordHasherFactory->getPasswordHasher(UserAccount::class)->hash("admin"));
        $admin->setName("ADMIN");
        $admin->setFirstName("Super");
        $manager->persist($admin);


        $manager->flush();
    }
}
