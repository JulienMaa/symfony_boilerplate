<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Afficher la liste des tâches.
    #[Route('/index', name: 'task_index')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/index.html.twig', [
            "tasks" => $tasks,
        ]);
    }

    // Créer une nouvelle tâche.
    #[Route('/create', name: 'task_create')]
    public function create(): Response
    {
        return $this->render('task/create.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
    
    // Modifier une tâche.
    #[Route('/edit/{id}', name: 'task_edit')]
    public function edit(int $id, TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        if (!$tasks) {
            $this->addFlash('error', 'No tasks were found.');
            return $this->redirectToRoute('home');
        }

        return $this->render('task/edit.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    // Voir une tâche.
    #[Route('/view/{id}', name: 'task_view')]
    public function view(int $id, TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        if (!$tasks) {
            throw new \NotFoundException();
        }

        return $this->render('task/view.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
    
    // Supprimer une tâche.
    #[Route('/delete/{id}', name: 'task_delete')]
    public function delete(int $id): Response
    {
        return $this->render('task/delete.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
