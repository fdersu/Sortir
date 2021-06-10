<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\LieuFormType;
use App\Form\MotifAnnulationType;
use App\Form\SortieFormType;
use App\ManageEntity\UpdateEntity;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Upload\SortieImages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * Methode de création ou modification d'une sortie
     * @Route("/sortie_add", name="sortie_add")
     * @Route ("/sortie_update/{sortie_id}", name="sortie_update", requirements={"sortie_id"="\d+"})
     */
    public function add(Request $request,SortieImages $images, UpdateEntity $updateEntity, EntityManagerInterface $entityManager, $sortie_id=null): Response
    {
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
            $file = $sortieForm->get('urlPhoto')->getData();
            if ($file) {
                $directory = $this->getParameter('upload_images_sortie_dir');
                $images->save($file, $sortie, $directory);
            }
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
                $updateEntity->save($sortie);
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie ajoutée !');

                return $this->redirectToRoute('sortie_detail', ['sortie_id' => $sortie->getId(), 'urlPhoto' => $sortie->getUrlPhoto()]);
            }
        }

        return $this->render('sortie/sortie.html.twig', [
            'sortieForm' => $sortieForm->createView(),
            'lieuForm' => $lieuForm->createView(),
            'sortie' => $sortie,
        ]);

    }

    /**
     * Methode de récupération de la liste des lieux pour le formulaire de creation de sortie
     * @Route("/sortie_add/lieu", name="sortie_add_lieu")
     * @Route("/sortie_update/lieu", name="sortie_update_lieu")
     */
    public function addSortieLieu(Request $request, VilleRepository $villeRepository): Response
    {

        $ville = new Ville();

        //S'il y a de la data en requete, récupération du json, décodage, et recherche de la ville d'après son id
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent());
            $ville = $villeRepository->find($data->ville);
        }

        //Récupération d'un tableau de lieux à partir de la ville
        $lieux = $ville->getLieus();

        //Recreation manuellement d'un tableau de lieux (pour éviter pb avec json.parse au décodage)
        $tableauLieux = [];
        foreach ($lieux as $lieu){
           array_push($tableauLieux, ['id' => $lieu->getId(), 'nom' => $lieu->getNom()]);
        }

        //Création d'une réponse Json encodant le tableau de lieux et envoi en retour
        $response = new JsonResponse(['lieux' => $tableauLieux]);
        $response->headers->set("Content-Type", "application/json;charset=utf-8");

        return $response;

    }

}
