<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\AssuranceRepository;

#[ORM\Entity(repositoryClass: AssuranceRepository::class)]
#[ORM\Table(name: 'assurance')]
class Assurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_contrat', type: 'integer')]
    private ?int $ID_contrat = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: 'La durée ne peut pas être vide')]
    #[Assert\Type(type: 'integer', message: 'La durée doit être un nombre entier')]
    #[Assert\Range(
        min: 1,
        max: 120,
        notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} mois',
    )]
    private ?int $Duree = null;

    #[ORM\Column(name: 'type_de_couverture', type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le type de couverture ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le type de couverture doit comporter au moins {{ limit }} caractères',
        maxMessage: 'Le type de couverture ne peut pas dépasser {{ limit }} caractères',
    )]
    private ?string $typeDeCouverture = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'La date de début ne peut pas être vide')]
    #[Assert\Date(message: 'La date n\'est pas valide')]
    private ?string $dateDebut = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le statut ne peut pas être vide')]
    #[Assert\Choice(
        choices: ['Active', 'En attente', 'Expirée'],
        message: 'Choisissez un statut valide',
    )]
    private ?string $Statut = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Les conditions ne peuvent pas être vides')]
    #[Assert\Length(
        min: 10,
        minMessage: 'Les conditions doivent comporter au moins {{ limit }} caractères',
    )]
    private ?string $Conditions = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'assurances')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner un utilisateur')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'assurance', cascade: ['persist', 'remove'])]
    private Collection $reclamations;

   public function getId(): ?int
    {
        return $this->ID_contrat;
    } 
    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
    }

    public function getID_contrat(): ?int
    {
        return $this->ID_contrat;
    }

    public function setID_contrat(int $ID_contrat): self
    {
        $this->ID_contrat = $ID_contrat;
        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->Duree;
    }

    public function setDuree(int $Duree): self
    {
        $this->Duree = $Duree;
        return $this;
    }

    public function getTypeDeCouverture(): ?string
    {
        return $this->typeDeCouverture;
    }

    public function setTypeDeCouverture(string $typeDeCouverture): self
    {
        $this->typeDeCouverture = $typeDeCouverture;
        return $this;
    }

    public function getDateDebut(): ?string
    {
        return $this->dateDebut;
    }

    public function setDateDebut(string $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;
        return $this;
    }

    public function getConditions(): ?string
    {
        return $this->Conditions;
    }

    public function setConditions(string $Conditions): self
    {
        $this->Conditions = $Conditions;
        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setAssurance($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // Set the owning side to null (unless already changed)
            if ($reclamation->getAssurance() === $this) {
                $reclamation->setAssurance(null);
            }
        }
        return $this;
    }
}
