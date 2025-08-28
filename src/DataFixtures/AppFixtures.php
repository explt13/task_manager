<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $task = new Task();
            $task->setTitle("Task $i");
            $task->setDescription("Description for task $i");
            $task->setStatus($i % 2 === 0 ? TaskStatus::Active : TaskStatus::Completed);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
