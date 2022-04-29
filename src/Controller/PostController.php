<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/register-post", name="registerPost")
     */
    public function index(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $post->setUser($user);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
