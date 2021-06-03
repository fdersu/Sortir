<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use App\Form\VilleFormType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie/detail/{sortie_id}", name="sortie_detail", requirements={"sortie_id"="\d+"})
     */
    public function detail(SortieRepository $sortieRepository, $sortie_id): Response
    {
        $sortie = $sortieRepository->find($sortie_id);
        $nbPlaces = $sortie->getNbInscriptionsMax() - $sortie->getInscriptions()->count();
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortie,
            'nbPlaces' => $nbPlaces
        ]);
    }

    /** @Route("/sortie/{sortie_id}/participant/{id}", name="sortie_participant", requirements={"sortie_id"="\d+","id"="\d+"}) */
    public function participant(UserRepository $userRepository,SortieRepository $sortieRepository, $sortie_id, $id){
        $user = $userRepository->find($id);
        $sortie = $sortieRepository->find($sortie_id);
        return $this->render('sortie/participant.html.twig', [
            'participant' => $user,
            'sortie' => $sortie
        ]);
    }

    /** @Route("/sortie/cancel/{sortie_id}", name="sortie_cancel", requirements={"sortie_id"="\d+"}) */
    public function cancel(EntityManagerInterface $entityManager,EtatRepository $etatRepository, SortieRepository $sortieRepository, $sortie_id){
        $sortie = $sortieRepository->find($sortie_id);
        $cancelled = $etatRepository->findOneBy(['libelle' => 'Annulée']);
        foreach ($sortie->getInscriptions() as $item){
            $entityManager->remove($item);
        }
        $entityManager->flush();
        $sortie->setEtat($cancelled);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('main_accueil');
    }

    /**
     * @Route("/sortie_add", name="sortie_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {

        $sortie = new Sortie();
        $lieu = new Lieu();

        /** @var User $user */
        $user = $this->getUser();
        $siteUser = $user->getSite()->getNom();

        $sortieForm = $this->createForm(SortieFormType::class, $sortie, ['site'=>$siteUser]);
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);


        //Methode pour setter Organisateur direct dans le controlleur :
        $sortie->setOrganisateur($entityManager->getRepository(User::class)->findOneBy(['pseudo' => $this->getUser()->getUsername()]));

        //Methode pour récupérer le site en fonction de l'organisateur
        $sortie->setSite($user->getSite());

        $sortieForm->handleRequest($request);
        $lieuForm->handleRequest($request);



        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            if($sortie->getLieu() !== null){

                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie ajoutée !');

                return $this->redirectToRoute('sortie_detail', ['sortie_id' => $sortie->getId()]);
            }
        }

        return $this->render('sortie/add.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
        ]);

    }

    /**
     * @Route("/sortie_lieu", name="sortie_lieu")
     */
    public function lieu(Request $request, EntityManagerInterface $entityManager): Response
    {

        $lieu = new Lieu();
        $lieux = $entityManager->getRepository(Lieu::class)->findAll();

        $lieuForm = $this->createForm(LieuFormType::class, $lieu);
        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $lieuForm->isValid()){

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajouté !');
            return $this->redirectToRoute('sortie_add');

        }


        return $this->render('sortie/lieu.html.twig', [
            'lieuForm' => $lieuForm->createView(),
            'lieux' => $lieux,
        ]);

    }

    /**
     * @Route("/sortie_ville", name="sortie_ville")
     */
    public function ville(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $villes = $entityManager->getRepository(Ville::class)->findAll();

        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', 'Ville ajoutée !');
            return $this->redirectToRoute('sortie_lieu');

        }


        return $this->render('sortie/ville.html.twig', [
            'villeForm' => $villeForm->createView(),
            'villes' => $villes,
        ]);

    }

}
