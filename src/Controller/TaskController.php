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


class TaskController extends AbstractController {

    private $em;
    private $taskRepository;

    public function __construct(EntityManagerInterface $em, TaskRepository $taskRepository)
    {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
    }

    #[Route('/tasks/add', name: 'add_task')]
    public function addTask(Request $request): Response {

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


    #[Route('/tasks/update/{id}', name: 'update_task')]
    public function updateTask($id, Request $request): Response
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Redirect back to the project details page
            return $this->redirectToRoute('show_project', ['id' => $task->getId()]);
        }

        return $this->render('task/update.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    
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
