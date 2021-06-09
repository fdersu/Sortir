<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Merci d'indiquer un nom pour votre sortie")
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @Assert\GreaterThan("+1 day", message="Vous ne pouvez pas créer de sortie avant demain")
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;

    /**
     * @Assert\Type("integer")
     * @Assert\Range(
     *     min="1",
     *     max="36",
     *     notInRangeMessage="La durée doit être comprise entre 1h et 36h",
     * )
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @Assert\LessThan(propertyPath="dateDebut", message="La date de cloture doit être antérieure à la date de la sortie")
     * @ORM\Column(type="datetime")
     */
    private $dateCloture;

    /**
     * @Assert\Type("integer")
     * @Assert\Range(
     *     min="2",
     *     max="50",
     *     notInRangeMessage="Le nombre de participants doit être compris entre 2 et 50",
     * )
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     *@Assert\NotBlank(message = "Merci de décrire la sortie en quelques mots")
     *@Assert\Length(
     *min="3",
     *max="500",
     *minMessage="Minimum 3 characters",
     *maxMessage="Maximum 500 characters"
     *)
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $urlPhoto;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity=Inscription::class, mappedBy="sortie")
     */
    private $inscriptions;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeInterface $dateCloture): self
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortie($this);
        }

        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }
}
