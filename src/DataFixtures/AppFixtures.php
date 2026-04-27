<?php

namespace App\DataFixtures;
use App\Enum\Status;
use App\Entity\Priority;
use App\Entity\Task;
use App\Entity\Folder;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Priorités
        $urgent = (new Priority())->setName('urgent')->setImportance(1);
        $important = (new Priority())->setName('important')->setImportance(2);
        $normal = (new Priority())->setName('normal')->setImportance(3);

        $manager->persist($urgent);
        $manager->persist($important);
        $manager->persist($normal);

        // User
        $user = (new User())
            ->setEmail("zut@gmail.com")
            ->setPassword("1234")
            ->setUsername("mechantloup");

        $manager->persist($user);

        // Folders
        $project = (new Folder())->setName('Project')->setUser($user);
        $sport = (new Folder())->setName('Sport')->setUser($user);

        $manager->persist($project);
        $manager->persist($sport);

        // Tasks
        $task1 = (new Task())
            ->setTitle('Créer la première tâche')
            ->setFolder($project)
            ->setPriorities($normal)
            ->setStatus(Status::PENDING)
            ->setIsPinned(false)
            ->setUser($user);

        $task2 = (new Task())
            ->setTitle('Faire le front')
            ->setFolder($project)
            ->setPriorities($urgent)
            ->setStatus(Status::COMPLETED)
            ->setIsPinned(true)
            ->setUser($user);

        $task3 = (new Task())
            ->setTitle('Tâche archivée')
            ->setFolder($project)
            ->setPriorities($important)
            ->setStatus(Status::ARCHIVED)
            ->setIsPinned(false)
            ->setUser($user);

        $task4 = (new Task())
            ->setTitle('Concours de hamac')
            ->setFolder($sport)
            ->setPriorities($important)
            ->setStatus(Status::ARCHIVED)
            ->setIsPinned(false)
            ->setUser($user);

        $manager->persist($task1);
        $manager->persist($task2);
        $manager->persist($task3);
        $manager->persist($task4);

        $manager->flush();
    }
}
