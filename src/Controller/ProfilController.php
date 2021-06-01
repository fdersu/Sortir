<?php

namespace App\Controller;

use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    /**
     * @Route("/edit{id}", name="profil_edit", requirements={"id"="\d+"})
     */
    public function edit($id, UserRepository $userRepository,
                         Request $request,
                         EntityManagerInterface $entityManager): Response
    {

        $user = $userRepository->find($id);
        if (!$user->getId()) {
            throw $this->createNotFoundException("Oops ! You can't visit this profil ! ");
        }

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            if($user ->get)
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Profil modified !!');

        return $this->render('profil/profil.html.twig', ['userForm' => $userForm->createView(),'id' => $user->getId()]);
    }
}