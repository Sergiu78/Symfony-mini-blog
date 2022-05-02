<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index(): Response
    {
        $em = $this->container->get('doctrine')->getManager();
        $posts = $em->getRepository(Post::class)->findAll();
        return $this->render('dashboard/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
