<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\AssuranceRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AssuranceRepository::class)]
#[ORM\Table(name: 'assurance')]
class Assurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_contrat', type: 'integer')]
    private ?int $ID_contrat = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: 'La durée est requise')]
    #[Assert\Positive(message: 'La durée doit être un nombre positif')]
    #[Assert\Range(
        min: 1,
        max: 120,
        notInRangeMessage: 'La durée doit être comprise entre {{ min }} et {{ max }} mois'
    )]
    private ?int $Duree = null;

    #[ORM\Column(name: 'type_de_couverture', type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le type de couverture est requis')]
    #[Assert\Choice(
        choices: ['accident', 'maladie', 'responsabilité civile', 'invalidité'],
        message: 'Le type de couverture doit être l\'un des suivants: accident, maladie, responsabilité civile, invalidité'
    )]
    private ?string $typeDeCouverture = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'La date de début est requise')]
    #[Assert\Date(message: 'La date n\'est pas valide')]
    #[Assert\Regex(
        pattern: '/^\d{4}-\d{2}-\d{2}$/',
        message: 'La date doit être au format YYYY-MM-DD'
    )]
    private ?string $dateDebut = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le statut est requis')]
    #[Assert\Choice(
        choices: ['Active', 'En attente', 'Expirée'],
        message: 'Le statut doit être l\'un des suivants: Active, En attente, Expirée'
    )]
    private ?string $Statut = 'En attente';

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Les conditions sont requises')]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: 'Les conditions doivent contenir au moins {{ limit }} caractères',
        maxMessage: 'Les conditions ne peuvent pas dépasser {{ limit }} caractères'
    )]
    private ?string $Conditions = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPaid = false;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'assurances')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner un utilisateur')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'assurance', cascade: ['persist', 'remove'])]
    private Collection $reclamations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    private $createdBy;

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

    public function getIsPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;
        // Mettre à jour automatiquement le statut en fonction du paiement
        $this->setStatut($isPaid ? 'Active' : 'En attente');
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

    public function getCreatedBy(): ?Utilisateur
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Utilisateur $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
