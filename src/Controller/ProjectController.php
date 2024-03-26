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

/**
 * Class ProjectController
 * @package App\Controller
 *
 * This controller manages operations related to projects.
 */
class ProjectController extends AbstractController
{
    private $em;
    private $projectRepository;

    public function __construct(EntityManagerInterface $em, ProjectRepository $projectRepository)
    {
        $this->em = $em;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Displays a list of all projects.
     *
     * @return Response The HTTP response containing the list of projects.
     *
     * #[Route('/projects', name: 'projects_list')]
     */
    #[Route('/projects', name: 'projects_list')]
    public function index(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * Adds a new project.
     *
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response for adding a project.
     *
     * #[Route('/projects/add', name: 'add_project')]
     */
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

    /**
     * Displays details of a specific project.
     *
     * @param Project $project The project entity to display.
     *
     * @return Response The HTTP response containing the project details.
     *
     * #[Route('/projects/{id}', methods: ['GET'], name: 'show_project')]
     */
    #[Route('/projects/{id}', methods: ['GET'], name: 'show_project')]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * Updates an existing project.
     *
     * @param int $id The ID of the project to update.
     * @param Request $request The HTTP request object.
     *
     * @return Response The HTTP response for updating a project.
     *
     * #[Route('/projects/update/{id}', name: 'update_project')]
     */
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

            return $this->redirectToRoute('show_project', ['id' => $project->getId()]);
        }

        return $this->render('project/update.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a project.
     *
     * @param int $id The ID of the project to delete.
     *
     * @return Response The HTTP response for deleting a project.
     *
     * #[Route('/projects/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_project')]
     */
    #[Route('/projects/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_project')]
    public function deleteProject($id): Response
    {
        $project = $this->projectRepository->find($id);
        $this->em->remove($project);
        $this->em->flush();

        return $this->redirectToRoute('projects_list');
    }
}
