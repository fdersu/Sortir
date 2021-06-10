<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionUsersController extends AbstractController
{
    /**
     * @Route("/gestion/users/enableDisableDelete", name="gestion_users_enable_disable_delete")
     */
    public function enableDisableDelete(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('main_accueil');
        }
        $allUsers = $userRepository->findAll();
        $users = [];
        foreach ($allUsers as $user) {
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                array_push($users, $user);
            }
        }
        $generalForm = $this->createFormBuilder();
        foreach ($users as $item) {
            /** @var User $user */
            $user = $item;
            $generalForm->add($user->getId(), ChoiceType::class, [
                'label' => $user->getPseudo(),
                'choices' => ['actif/ve' => true, 'inactif/ve' => false, 'supprimer' => 'delete'],
                'expanded' => true,
                'multiple' => false
            ]);
            $generalForm->get($user->getId())->setData($user->getActif());
        }
        $formActive = $generalForm->getForm();
        $formActive->handleRequest($request);

        if ($formActive->isSubmitted() && $formActive->isValid()) {
            foreach ($users as $item) {
                /** @var User $user */
                $user = $item;
                $data = $formActive->get($user->getId())->getData();
                if ($data === true) {
                    $user->setActif(true);
                    $entityManager->persist($user);
                } elseif ($data === false) {
                    $user->setActif(false);
                    $entityManager->persist($user);
                } elseif ($data === 'delete') {
                    foreach ($user->getInscriptions() as $ins) {
                        $entityManager->remove($ins);
                    }
                    $entityManager->flush();
                    foreach ($user->getSorties() as $sortie) {
                        foreach ($sortie->getInscriptions() as $inscription) {
                            $entityManager->remove($inscription);
                        }
                        $entityManager->flush();
                        $entityManager->remove($sortie);
                    }
                    $entityManager->flush();
                    $entityManager->remove($user);
                }
            }
            $entityManager->flush();
            $this->addFlash('success', 'Modifications effectuées');
            return $this->redirectToRoute('main_accueil');
        }

        return $this->render('gestion_users/gestionUtilisateurs.html.twig', [
            'formActive' => $formActive->createView(),
            'allUsers' => $users
        ]);
    }


    /**
     * Methode pour exporter tous les users au format CSV (pour avoir un fichier pour tester l'import)
     * @Route("/gestion/users/export", name="users_export")
     */
    public function exportCSV(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();
        $str = "site;pseudo;password;nom;prenom;telephone;mail;actif;roles;photo"."\n";

        foreach ($users as $user) {

            $str .= $user->getSite()->getNom() . ";" . $user->getPseudo() . ";" . $user->getPassword() . ";" . $user->getNom() . ";" . $user->getPrenom();
            $str .= ";" . $user->getTelephone() . ";" . $user->getMail() . ";" . $user->getActif() . ";" . $user->getRolesToString() . ";" . $user->getPhoto();
            $str .= "\n";
        }

        $response = new Response($str);
        $response->headers->set('Content-Type', 'text/csv');

        return $response;
    }
}
