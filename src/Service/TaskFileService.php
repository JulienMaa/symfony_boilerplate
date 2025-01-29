<?php

namespace App\Service;

use App\Entity\Task;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Uid\Uuid;

class TaskFileService
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    //  Ajouter une tâche
    public function createTask(string $title, string $description): void
    {
        try {
            $uid = uniqid();

            if (!$filesystem->exists('/public/tasks')) {
                $filesystem->mkdir('/public/tasks');
            }
        
            $filesystem->dumpFile('/public/tasks/' . $uid . '.txt', 'Titre : ' . $title . '\nDescription : ' . $description);

        } catch (\IOExceptionInterface $e) {
            throw new IOExceptionInterface("Error : " . $e->getMessage);
        }
    }

    //  Modifier une tâche
    public function updateTask(string $id, string $title, string $description): bool
    {
        try {
            if (!$filesystem->exists('/public/tasks/' . $id . '.txt')) {
                throw new IOExceptionInterface("Task not found");
            }

            $filesystem->dumpFile('/public/tasks/' . $id . '.txt', 'Titre : ' . $title . '\nDescription : ' . $description);

        } catch (\IOExceptionInterface $e) {
            throw new IOExceptionInterface("Error : " . $e->getMessage);
        }
    }

    //  Lister toutes les tâches
    public function listTasks(): array
    {
        try {
            $tasks = [];
            if (!$filesystem->exists('/public/tasks')) {
                return $tasks;
            }

            $files = $filesystem->getFiles('/public/tasks');
            foreach ($files as $file) {
                $task = $filesystem->readFile('/public/tasks/' . $file);
                $tasks[] = $task;
            }

            return $tasks;
        } catch (\IOExceptionInterface $e) {
            throw new IOExceptionInterface("Error : " . $e->getMessage);
        }
    }

    //  Afficher les détails d’une tâche, retourne les informations de la tâche (titre, description, date de création) sous forme d’un tableau.
    public function getTask(string $id): array
    {
        try {
            
        } catch (\IOExceptionInterface $e) {
            throw new IOExceptionInterface("Error : " . $e->getMessage);
        }
    }

    //  Supprimer une tâche
    public function deleteTask(string $id): bool
    {
        try {
            if (!$filesystem->exists('/public/tasks/' . $id . '.txt')) {
                throw new IOExceptionInterface("Task not found");
            }
    
            $filesystem->deleteFile('/public/tasks/' . $id . '.txt');
            return true;
        } catch (\IOExceptionInterface $e) {
            throw new IOExceptionInterface("Error : " . $e->getMessage);
        }
    }
}
