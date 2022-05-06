<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostController extends AbstractController
{
    /**
     * @Route("/register-post", name="registerPost")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('foto')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Sorry something went wrong!!!');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setFoto($newFilename);
            }
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

    /**
     * @Route("/post/{id}", name="showPost")
     */
    public function showPost($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        return $this->render('post/showPost.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/posts", name="myPosts")
     */
    public function userPosts()
    {
        $em = $this->container->get('doctrine')->getManager();
        $user = $this->getUser();
        $posts = $em->getRepository(Post::class)->findBy(['user' => $user]);
        return $this->render('post/userPosts.html.twig', ['posts' => $posts]);
    }
}
