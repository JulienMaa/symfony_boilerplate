<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Form\TaskType;
use App\Security\Voter\TaskVoter;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TaskService $taskService;

    public function __construct(EntityManagerInterface $entityManager, TaskService $taskService)
    {
        $this->entityManager = $entityManager;
        $this->taskService = $taskService;
    }

    // Afficher la liste des tâches.
    #[Route('/task/index', name: 'task_index')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();

        return $this->render('task/index.html.twig', [
            "tasks" => $tasks,
        ]);
    }

    // Créer une nouvelle tâche.
    #[Route('/task/create', name: 'task_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $task = new Task();

        // Ensure the user is retrieved correctly
        $user = $security->getUser();
        if ($user) {
            $task->setAuthor($user); // Assign the current user as the author
            $task->updateTimestamps();
        } else {
            $this->addFlash('error', 'No user found, try to app_login.');
            return $this->redirectToRoute('task_index');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($task);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
                return $this->redirectToRoute('task_index');
            }

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'TaskController',
        ]);
    }
    
    // Modifier une tâche (Edit a task)
    #[Route('/task/edit/{id}', name: 'task_edit')]
    public function edit(int $id, TaskRepository $taskRepository, Request $request): Response
    {
        $task = $taskRepository->find($id);

        if (!$this->isGranted(TaskVoter::EDIT, $task)) {
            $this->addFlash('error', 'You do not have permission to edit this task.');
            return $this->redirectToRoute('app_login');
        }

        if (!$task) {
            $this->addFlash('error', 'Task not found.');
            return $this->redirectToRoute('task_index');
        }

        if (!$this->taskService->canEdit($task)) {
            $this->addFlash('error', 'This task cannot be edited because it was created more than 7 days ago.');
            return $this->redirectToRoute('task_index');
        }

        if ($request->isMethod('POST')) {
            $task->setName($request->request->get('name'))
                ->setDescription($request->request->get('description'))
                ->updateTimestamps();

            $this->entityManager->flush();

            $this->addFlash('success', 'Task updated successfully!');
            return $this->redirectToRoute('task_index', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
        ]);
    }
 
    // Voir une tâche (View a task)
    #[Route('/task/view/{id}', name: 'task_view')]
    public function view(int $id, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);

        // Check if the current user can view the task
        if (!$this->isGranted(TaskVoter::VIEW, $task)) {
            $this->addFlash('error', 'You do not have permission to edit this task. Only the owner of the task or admins can view it.');
            return $this->redirectToRoute('app_login');
        }
        
        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }
 
    // Supprimer une tâche (Delete a task)
    #[Route('/task/delete/{id}', name: 'task_delete')]
    public function delete(int $id, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $task = $taskRepository->find($id);

        if (!$this->isGranted(TaskVoter::DELETE, $task)) {
            $this->addFlash('error', 'You do not have permission to edit this task. Only the owner of the task or admins can delete it.');
            return $this->redirectToRoute('app_login');
        }
        
        if (!$task) {
            $this->addFlash('error', 'Task not found.');
            return $this->redirectToRoute('task_index');
        }
    
        $entityManager->remove($task);
        $entityManager->flush();
        
        $this->addFlash('success', 'Task deleted successfully.');
        
        return $this->redirectToRoute('task_index');
    }
}
