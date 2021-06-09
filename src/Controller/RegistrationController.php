<?php

namespace App\Controller;

use App\Command\AddUsersFromFilesCommand;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
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

            //Envoyer les données en bdd
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

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
        EntityManagerInterface $entityManager,
        string $dataDirectory
    )
    {
        //Upload du fichier dans le dossier public/data
        $nomOrigine = $_FILES['monfichier']['name'];
        $elementsChemin = pathinfo($nomOrigine);
        $extensionFichier = $elementsChemin['extension'];
        $extensionsAutorisees = array("csv", "xml");

        if (!(in_array($extensionFichier, $extensionsAutorisees))) {
            echo "Le fichier n'a pas l'extension attendue";
        } else {
            // Copie dans le repertoire du script avec un nom incluant l'heure a la seconde pres
            $repertoireDestination =  $dataDirectory;
            $filename = "addUsers".date("dmYHis").".".$extensionFichier;

            if (move_uploaded_file($_FILES["monfichier"]["tmp_name"],
                $repertoireDestination.$filename)) {
                echo "Le fichier temporaire ".$_FILES["monfichier"]["tmp_name"].
                    " a été déplacé vers ".$repertoireDestination.$filename;
            } else {
                echo "Le fichier n'a pas été uploadé (trop gros ?) ou ".
                    "Le déplacement du fichier temporaire a échoué".
                    " vérifiez l'existence du répertoire ".$repertoireDestination;
            }
        }

        //Lecture du fichier et ajout des utilisateurs
        $users = $addUsersFromFilesCommand->addUsers($filename);
        foreach ($users as $addUserOneByOne) {

            $entityManager->persist($addUserOneByOne);
        }
        $entityManager->flush();
        
        $this->addFlash('success', 'Utilisateurs ajoutés !');
        return $this->redirectToRoute('app_register');
    }
}

