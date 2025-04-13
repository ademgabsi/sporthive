<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AssuranceRepository;

#[ORM\Entity(repositoryClass: AssuranceRepository::class)]
#[ORM\Table(name: 'assurance')]
class Assurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $ID_contrat = null;

    public function getID_contrat(): ?int
    {
        return $this->ID_contrat;
    }

    public function setID_contrat(int $ID_contrat): self
    {
        $this->ID_contrat = $ID_contrat;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $Duree = null;

    public function getDuree(): ?int
    {
        return $this->Duree;
    }

    public function setDuree(int $Duree): self
    {
        $this->Duree = $Duree;
        return $this;
    }

    #[ORM\Column(name: 'type_de_couverture', type: 'string', nullable: false)]
    private ?string $typeDeCouverture = null;

    public function getTypeDeCouverture(): ?string
    {
        return $this->typeDeCouverture;
    }

    public function setTypeDeCouverture(string $typeDeCouverture): self
    {
        $this->typeDeCouverture = $typeDeCouverture;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $dateDebut = null;

    public function getDateDebut(): ?string
    {
        return $this->dateDebut;
    }

    public function setDateDebut(string $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Statut = null;

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Conditions = null;

    public function getConditions(): ?string
    {
        return $this->Conditions;
    }

    public function setConditions(string $Conditions): self
    {
        $this->Conditions = $Conditions;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'assurances')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
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

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'assurance')]
    private Collection $reclamations;

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        if (!$this->reclamations instanceof Collection) {
            $this->reclamations = new ArrayCollection();
        }
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->getReclamations()->contains($reclamation)) {
            $this->getReclamations()->add($reclamation);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        $this->getReclamations()->removeElement($reclamation);
        return $this;
    }

}
