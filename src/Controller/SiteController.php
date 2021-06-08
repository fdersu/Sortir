<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site_add", name="site_add")
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
            return $this->redirectToRoute('site_add');

        }


        return $this->render('sortie/site.html.twig', [
            'siteForm' => $siteForm->createView(),
            'sites' => $sites,
        ]);

    }

    /**
     * @Route ("/site_update/{site_id}", name="site_update", requirements={"site_id"="\d+"})
     */
    public function updateSite(Request $request, EntityManagerInterface $entityManager, $site_id=null): Response
    {
        $site = $entityManager->getRepository(Site::class)->find($site_id);

        $sites = $entityManager->getRepository(Site::class)->findAll();

        $siteForm = $this->createForm(SiteFormType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            $entityManager->persist($site);
            $entityManager->flush();

            $this->addFlash('success', 'Site modifié !');
            return $this->redirectToRoute('site_add');

        }


        return $this->render('sortie/site.html.twig', [
            'siteForm' => $siteForm->createView(),
            'sites' => $sites,
        ]);

    }

    /**
     * @Route("/site_delete/{site_id}", name="site_delete")
     */
    public function delete($site_id, EntityManagerInterface $entityManager): Response
    {
        $siteToDelete = $entityManager->find(Site::class, $site_id);

        $entityManager->remove($siteToDelete);
        $entityManager->flush();

        $this->addFlash('success', 'Le site a été supprimé');

        return $this->redirectToRoute('site_add');
    }

}