<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\EquipeRepository;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
#[ORM\Table(name: 'equipe')]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom de l'équipe doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'équipe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "La ville est obligatoire.")]
    private ?string $ville = null;

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "L'année de fondation est obligatoire.")]
    #[Assert\LessThan("today", message: "L'année de fondation doit être dans le passé.")]
    private ?\DateTimeInterface $annee_fondation = null;

    public function getAnneeFondation(): ?\DateTimeInterface
    {
        return $this->annee_fondation;
    }

    public function setAnneeFondation(\DateTimeInterface $anneeFondation): self
    {
        $this->annee_fondation = $anneeFondation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "Le nom du stade est obligatoire.")]
    private ?string $stade = null;

    public function getStade(): ?string
    {
        return $this->stade;
    }

    public function setStade(string $stade): self
    {
        $this->stade = $stade;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $logo = null;

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'equipes')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Joueur::class, mappedBy: 'equipe')]
    private Collection $joueurs;

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs[] = $joueur;
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        $this->joueurs->removeElement($joueur);

        return $this;
    }

    // Constructeur pour initialiser la collection
    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }
}
