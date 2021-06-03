<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ProfilController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="profil_edit", requirements={"id"="\d+"})
     */
    public function edit($id, UserRepository $userRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         UserPasswordEncoderInterface $passwordEncoder,
                         AuthenticationUtils $authenticationUtils): Response
    {


        $lastUsername = $authenticationUtils->getLastUsername();
        $error = "";
        $userInSession = $userRepository->findOneBy(["pseudo" => $this->getUser()->getUsername()]);
        $user = $userRepository->find($id);
        $userPseudo = new User();
        $userPseudo->setPseudo($user->getPseudo());


        if (!$user) {
            throw $this->createNotFoundException("Oops ! This user does not exist ! ");
        }

        if ($userInSession !== $user) {
            throw $this->createNotFoundException("Oops ! You can't edit another profil than your's ! ");
        }

        try {
            $userForm = $this->createForm(UserType::class, $userInSession);
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {


                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $userInSession,
                        $userForm->get('password')->getData()
                    ));

                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Profil modified !!');
            }

        } catch (\Exception $error) {
            error_log($error->getMessage());
        }
        $entityManager->refresh($user);
        return $this->render('profil/profil.html.twig', ['error' => $error, 'userForm' => $userForm->createView(), 'id' => $user->getId()]);
    }

}

