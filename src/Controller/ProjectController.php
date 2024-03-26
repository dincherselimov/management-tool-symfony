<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    private $em;
    private $projectRepository;

    public function __construct(EntityManagerInterface $em, ProjectRepository $projectRepository)
    {
        $this->em = $em;
        $this->projectRepository = $projectRepository;
    }

    #[Route('/projects', name: 'projects_list')]
    public function index(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/projects/add', name: 'add_project')]
    public function addProject(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectFormType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newProject = $form->getData();
            $this->em->persist($newProject);
            $this->em->flush();

            return $this->redirectToRoute('projects_list');
        }

        return $this->render('project/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/{id}', methods: ['GET'], name: 'show_project')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/projects/update/{id}', name: 'update_project')]
    public function updateProject($id, Request $request): Response
    {
        $project = $this->projectRepository->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $form = $this->createForm(ProjectFormType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            // Redirect back to the project details page
            return $this->redirectToRoute('show_project', ['id' => $project->getId()]);
        }

        return $this->render('project/update.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_project')]
    public function deleteProject($id): Response
    {
        $project = $this->projectRepository->find($id);
        $this->em->remove($project);
        $this->em->flush();

        return $this->redirectToRoute('projects_list');
    }
}
