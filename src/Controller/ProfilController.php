<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\ManageEntity\UpdateEntity;
use App\Repository\UserRepository;
use App\Upload\UserImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="profil_edit", requirements={"id"="\d+"})
     */
    public function edit($id, UserRepository $userRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         UserPasswordEncoderInterface $passwordEncoder,
                         UserImages $image,
                         UpdateEntity $updateEntity): Response
    {

        $error = "";

        $userInSession = $userRepository->findOneBy(["pseudo" => $this->getUser()->getUsername()]);
        $user = $userRepository->find($id);

        //Si l'id n'existe pas
        if (!$user) {
            throw $this->createNotFoundException("Oops ! This user does not exist ! ");
        }

        //Si l'id existe mais qu'il ne correspond pas au pseudo de l'utilisateur en session
        if ($userInSession !== $user) {
            throw $this->createNotFoundException("Oops ! You can't edit another profil than your's ! ");
        }

        try {
            //Création du formulaire
            $userForm = $this->createForm(UserType::class, $userInSession);

            //Recupérer les données dans le POST
            $userForm->handleRequest($request);

            if ($userForm->isSubmitted() && $userForm->isValid()) {
                $file = $userForm->get('photo')->getData();

                /**
                 * @var UploadedFile $file
                 */
                if ($file) {
                    $directory = $this->getParameter('upload_images_sortie_dir');
                    $image->save($file, $user, $directory);
                    $updateEntity->save($user);
                }

                //Encoder le password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $userInSession,
                        $userForm->get('password')->getData()
                    ));

                $entityManager->persist($user);
                $entityManager->flush();

                //Affichage du message si profil modifié
                $this->addFlash('success', 'Profil modified !!');
            }

        } catch (\Exception $error) {
            error_log($error->getMessage());
        }
        $entityManager->refresh($user);
        return $this->render('profil/profil.html.twig', ['user' => $user, 'error' => $error, 'userForm' => $userForm->createView(),
                                                              'id' => $user->getId(), 'photo' => $user->getPhoto()]);
    }


    /**
     * @Route ("/actif_update", name="actif_update")
     */ /*
    public function actifUpdate(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Récupération des users en base
        $users = $entityManager->getRepository(User::class)->findAll();

        if ($request->isMethod('post')) {
            //Pour chaque user, récupération de la checkbox corrrespondant et set
            foreach ($users as $user){
                $actif = $request->request->get($user->getId());
                if($actif!=null){
                    $user->setActif(true);
                } else {
                    $user->setActif(false);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Users modifiés !');
        }

        return $this->render('gestion_users/usersActifs_flxgrd.html.twig', [
            'users' => $users,
        ]);
    } */

}

