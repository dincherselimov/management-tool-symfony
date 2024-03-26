<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 *
 * This controller manages operations related to tasks.
 */
class TaskController extends AbstractController
{

    private $em;
    private $taskRepository;

    public function __construct(EntityManagerInterface $em, TaskRepository $taskRepository)
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;

    }

    /**
     * Adds a new task.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response for adding a task.
     *
     * #[Route('/tasks/add', name: 'add_task')]
     */
    #[Route('/tasks/add', name: 'add_task')]
    public function addTask(Request $request): Response
    {

        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newTask = $form->getData();
            $this->em->persist($newTask);
            $this->em->flush();

            return $this->redirectToRoute('projects_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Updates an existing task.
     *
     * @param int $id The ID of the task to update.
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response for updating a task.
     *
     * #[Route('/tasks/update/{id}', name: 'update_task')]
     */
    #[Route('/tasks/update/{id}', name: 'update_task')]
    public function updateTask($id, Request $request): Response
    {
        $task = $this->taskRepository->find($id);
        $project = $task->getProjectId();

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('show_project', ['id' => $project->getId()]);

        }

        return $this->render('task/update.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

     /**
     * Deletes a task.
     *
     * @param int $id The ID of the task to delete.
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response for deleting a task.
     *
     * #[Route('/tasks/delete/{id}', methods: ['DELETE'], name: 'delete_task')]
     */
    #[Route('/tasks/delete/{id}', methods: ['DELETE'], name: 'delete_task')]
    public function deleteTask($id, Request $request): Response
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $this->em->remove($task);
        $this->em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

}
