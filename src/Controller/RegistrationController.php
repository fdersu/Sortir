<?php

namespace App\Controller;

use App\Command\AddUsersFromFilesCommand;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Upload\UserFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @Route("/update", name="app_update")
     * Incrire une nouvelle personne si admin
     */
    public function register(Request $request,
                             UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler,
                             AppAuthenticator $authenticator,
                             UserRepository $userRepository
    ): Response
    {


        if ($request->query->get('id')) {
            $id = $request->query->get('id');
            $user = $userRepository->find($id);
        } else {
            $user = new User();
        }

        //Création du formulaire
        $form = $this->createForm(RegistrationFormType::class, $user);

        //Récuperer les données dans le POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Encoder le mot de passe
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setActif(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }


        return $this->render('registration/register.html.twig', ['registrationForm' => $form->createView(),]);
    }

    /**
     * @Route("/register/file", name="app_register_file")
     * Incrire une nouvelle personne si admin via file
     */
    public function addUsersByFile(
        AddUsersFromFilesCommand $addUsersFromFilesCommand,
        Request $request,
        UserFile $userFile,
        EntityManagerInterface $entityManager
    )
    {

        $users = $request->request->get('importUser');
     
// Copie dans le repertoire du script avec un nom
// incluant l'heure a la seconde pres
        $repertoireDestination = dirname(__FILE__) . "/";
        $nomDestination = "fichier_du_" . date("YmdHis") . ".txt";
        if (is_uploaded_file($_FILES["monfichier"]["tmp_name"])) {
            if (rename($_FILES["monfichier"]["tmp_name"],
                $repertoireDestination . $nomDestination)) {
                echo "Le fichier temporaire " . $_FILES["monfichier"]["tmp_name"] .
                    " a été déplacé vers " . $repertoireDestination . $nomDestination;
            } else {
                echo "Le déplacement du fichier temporaire a échoué" .
                    " vérifiez l'existence du répertoire " . $repertoireDestination;
            }
        } else {
            echo "Le fichier n'a pas été uploadé (trop gros ?)";
        }
        $directory = $this->getParameter('upload_users_sortie_dir');

        $userFile->save($importUser, $directory);

        $users = $addUsersFromFilesCommand->addUsers();
        foreach ($users as $addUserOneByOne) {

            //$entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($addUserOneByOne);

        }

        $entityManager->flush();
        $this->addFlash('success', 'Utilisateurs ajoutés !');
        $entityManager->refresh($importUser);
        return $this->render('registration/register.html.twig');
    }
}

