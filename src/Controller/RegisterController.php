<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->container->get('doctrine')->getManager();
            $user->setPassword($passwordHasher->hashPassword($user, $form['password']->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', User::REGISTER_SUCCESS);
            return $this->redirectToRoute('app_register');
        }
        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'register_form' => $form->createView()
        ]);
    }
}
