<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\MotifAnnulationType;
use App\Form\SiteFormType;
use App\Form\SortieFormType;
use App\Form\VilleFormType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /** Affichage du détail d'une sortie */
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

    /** Affichage du profil d'un participant */
    /** @Route("/sortie/{sortie_id}/participant/{id}", name="sortie_participant", requirements={"sortie_id"="\d+","id"="\d+"}) */
    public function participant(UserRepository $userRepository,SortieRepository $sortieRepository, $sortie_id, $id){
        $user = $userRepository->find($id);
        $sortie = $sortieRepository->find($sortie_id);
        return $this->render('sortie/participant.html.twig', [
            'participant' => $user,
            'sortie' => $sortie
        ]);
    }

    /** @Route("/sortie/publier/{sortie_id}", name="sortie_publier", requirements={"sortie_id"="\d+"}) */
    public function publier(EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository, $sortie_id){
        $sortie = $sortieRepository->find($sortie_id);
        $newState = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
        $sortie->setEtat($newState);
        $entityManager->persist($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('main_accueil');
    }

    /** Annulation d'une sortie */
    /** @Route("/sortie/cancel/reason/{sortie_id}", name="sortie_cancelReason", requirements={"sortie_id"="\d+"}) */
    public function defineCancelReason(EntityManagerInterface $entityManager,EtatRepository $etatRepository, SortieRepository $sortieRepository,Request $request, $sortie_id){
        $sortie = $sortieRepository->find($sortie_id);
        $nbPlaces = $sortie->getNbInscriptionsMax() - $sortie->getInscriptions()->count();
        $cancelForm = $this->createForm(MotifAnnulationType::class, $sortie);
        $cancelForm->handleRequest($request);
        if($cancelForm->isSubmitted() && $cancelForm->isValid()){
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
        return $this->render('sortie/detail.html.twig', [
            'cancelForm' => $cancelForm->createView(),
            'sortie' => $sortie,
            'nbPlaces' => $nbPlaces
        ]);

    }

    /**
     * @Route("/sortie_add", name="sortie_add")
     * @Route ("/sortie_update/{sortie_id}", name="sortie_update", requirements={"sortie_id"="\d+"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager, $sortie_id=null): Response
    {
        dump('Sortie id en controller : '.$sortie_id);
        //Si aucun id de sortie en requete, creation de nouveaux objets sortie et lieu
        if($sortie_id!==null){
            $sortie = $entityManager->getRepository(Sortie::class)->find($sortie_id);
            $lieu = $sortie->getLieu();
        } else {
            $sortie = new Sortie();
            $lieu = new Lieu();
        }

        //Récupération du site de rattachement de l'organisateur
        /** @var User $user */
        $user = $this->getUser();
        $siteUser = $user->getSite()->getNom();
        $sortie->setSite($user->getSite());

        //Récupération de la longitude et latitude


        //Génération des formulaires Sortie et Lieu
        $sortieForm = $this->createForm(SortieFormType::class, $sortie, ['site'=>$siteUser]);
        $lieuForm = $this->createForm(LieuFormType::class, $lieu);


        //Methode pour setter Organisateur directement dans le controlleur :
        $sortie->setOrganisateur($entityManager->getRepository(User::class)->findOneBy(['pseudo' => $this->getUser()->getUsername()]));

        //Recupération de la requete
        $sortieForm->handleRequest($request);
        $lieuForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            //Vérification du bouton cliqué
            if ($sortieForm->getClickedButton() === $sortieForm->get('save')) {
                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']));

            } elseif ($sortieForm->getClickedButton() === $sortieForm->get('publish')){
                $sortie->setEtat($entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
            }

            //Récupération de l'id du lieu et set de l'objet à la place
            $idLieu = $sortieForm->get('lieu')->getData();
            $sortie->setLieu($entityManager->getRepository(Lieu::class)->find($idLieu));

            if($sortie->getLieu() !== null){

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie ajoutée !');

                return $this->redirectToRoute('sortie_detail', ['sortie_id' => $sortie->getId()]);
            }
        }

        return $this->render('sortie/add.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'sortie' => $sortie,
        ]);

    }

    /**
     * @Route("/sortie_add/lieu", name="sortie_add_lieu")
     */
    public function addSortieLieu(Request $request, VilleRepository $villeRepository): Response
    {

        $ville = new Ville();
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent());
            $ville = $villeRepository->find($data->ville);
        }

        $lieux = $ville->getLieus();

        $tableauLieux = [];

        foreach ($lieux as $lieu){
           array_push($tableauLieux, ['id' => $lieu->getId(), 'nom' => $lieu->getNom()]);
        }

        $response = new JsonResponse(['lieux' => $tableauLieux]);
        $response->headers->set("Content-Type", "application/json;charset=utf-8");

        return $response;

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

    /**
     * @Route("/sortie_site", name="sortie_site")
     */
    public function site(Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $sites = $entityManager->getRepository(Site::class)->findAll();

        $siteForm = $this->createForm(SiteFormType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            $entityManager->persist($site);
            $entityManager->flush();

            $this->addFlash('success', 'Site ajouté !');
            return $this->redirectToRoute('sortie_lieu');

        }


        return $this->render('sortie/site.html.twig', [
            'siteForm' => $siteForm->createView(),
            'sites' => $sites,
        ]);

    }

}
