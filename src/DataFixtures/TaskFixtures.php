<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $adminUser = $this->getReference(UserFixtures::ADMIN_REFERENCE);

        $task1 = new Task();
        $task1->setName('Task 1')
              ->setDescription('This is task 1')
              ->setCreatedAt((new \DateTime())->modify('-5 days'))
              ->setAuthor($adminUser);
        $manager->persist($task1);

        $task2 = new Task();
        $task2->setName('Task 2')
              ->setDescription('This is task 2')
              ->setCreatedAt((new \DateTime())->modify('-10 days'))
              ->setAuthor($adminUser);
        $manager->persist($task2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
