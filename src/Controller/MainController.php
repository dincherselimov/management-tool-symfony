<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Using this main controller for my main page
 */
class MainController extends AbstractController {

    #[Route('/', name: 'main_page')]
    public function index(): Response {
        return $this->render('main/index.html.twig');
    }
}
