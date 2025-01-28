<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public const ADMIN_REFERENCE = 'admin-user';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $adminUser = new User();
        $adminUser->setUsername('Administrator')
            ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
            ->setPassword(password_hash('supersecureadminpass trust', PASSWORD_BCRYPT));

        $manager->persist($adminUser);
        $this->addReference(self::ADMIN_REFERENCE, $adminUser);

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setUsername($faker->unique()->userName())
                ->setRoles(["ROLE_USER"])
                ->setPassword(password_hash($faker->password(), PASSWORD_BCRYPT));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
