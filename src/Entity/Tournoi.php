<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use App\Repository\TournoiRepository;

#[ORM\Entity(repositoryClass: TournoiRepository::class)]
#[ORM\Table(name: 'tournoi')]
class Tournoi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $lieu = null;

    #[ORM\ManyToOne(targetEntity: Competition::class, inversedBy: 'tournois')]
    #[ORM\JoinColumn(name: 'competition_id', referencedColumnName: 'ID')]
    private ?Competition $competition = null;

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
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->date_debut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->date_fin = $dateFin;
        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getCompetition(): ?Competition
    {
        return $this->competition;
    }

    public function setCompetition(?Competition $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    // 🚨 Validation personnalisée pour les dates
    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if ($this->date_debut && $this->date_fin) {
            if ($this->date_debut > $this->date_fin) {
                $context->buildViolation('La date de début doit être antérieure à la date de fin.')
                    ->atPath('date_debut')
                    ->addViolation();
            }
        }
    }

    // 🚨 Validation personnalisée pour le nom
    #[Assert\Callback]
    public function validateNom(ExecutionContextInterface $context): void
    {
        if (empty($this->nom)) {
            $context->buildViolation('🚨Le nom du tournoi est obligatoire.')
                ->atPath('nom')
                ->addViolation();
        } elseif (strlen($this->nom) < 3) {
            $context->buildViolation('🚨Le nom doit comporter au moins 3 caractères.')
                ->atPath('nom')
                ->addViolation();
        } elseif (strlen($this->nom) > 255) {
            $context->buildViolation('🚨Le nom ne peut pas dépasser 255 caractères.')
                ->atPath('nom')
                ->addViolation();
        }
    }
}
