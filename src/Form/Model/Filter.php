<?php


namespace App\Form\Model;


/** Objet filtre utilisé pour la création du formulaire du filtre */
class Filter
{

    private $site;
    private $recherche;
    private $dateDebut;
    private $dateFin;
    private $organisateur;
    private $inscrit;
    private $nonInscrit;
    private $sortiesPassees;

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site): void
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getRecherche()
    {
        return $this->recherche;
    }

    /**
     * @param mixed $recherche
     */
    public function setRecherche($recherche): void
    {
        $this->recherche = $recherche;
    }

    /**
     * @return mixed
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param mixed $dateDebut
     */
    public function setDateDebut($dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     */
    public function setDateFin($dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     */
    public function setInscrit($inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getNonInscrit()
    {
        return $this->nonInscrit;
    }

    /**
     * @param mixed $nonInscrit
     */
    public function setNonInscrit($nonInscrit): void
    {
        $this->nonInscrit = $nonInscrit;
    }

    /**
     * @return mixed
     */
    public function getSortiesPassees()
    {
        return $this->sortiesPassees;
    }

    /**
     * @param mixed $sortiesPassees
     */
    public function setSortiesPassees($sortiesPassees): void
    {
        $this->sortiesPassees = $sortiesPassees;
    }

}