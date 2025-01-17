<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = new User();
        $user->setUsername('Administrator')
                 ->setRoles(["ROLE_USER", "ROLE_ADMIN"])
                 ->setPassword(password_hash('supersecureadminpass trust', PASSWORD_BCRYPT));

        $manager->persist($user);

        for ($i = 0; $i < 50; $i++) {
            $userName = $faker->unique()->userName();
            $roles = random_int(1, 5) == 5 ? ["ROLE_USER", "ROLE_ADMIN"] : ["ROLE_USER"];

            $user = new User();
            $user->setUsername($userName)
                 ->setRoles($roles)
                 ->setPassword(password_hash($userName . "password", PASSWORD_BCRYPT));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
