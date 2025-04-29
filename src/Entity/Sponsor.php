<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\SponsorRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
#[ORM\Table(name: 'sponsor')]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_Sp = null;

    public function getId_Sp(): ?int
    {
        return $this->id_Sp;
    }

    public function setId_Sp(int $id_Sp): self
    {
        $this->id_Sp = $id_Sp;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le nom du sponsor est requis')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom_Sp = null;

    public function getNom_Sp(): ?string
    {
        return $this->nom_Sp;
    }
    
    // Alias for form compatibility
    public function getNom(): ?string
    {
        return $this->nom_Sp;
    }

    public function setNom_Sp(string $nom_Sp): self
    {
        $this->nom_Sp = $nom_Sp;
        return $this;
    }
    
    // Alias for form compatibility
    public function setNom(string $nom): self
    {
        return $this->setNom_Sp($nom);
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le type de sponsor est requis')]
    #[Assert\Choice(
        choices: ['delice', 'secondaire', 'technique', 'équipementier'],
        message: 'Le type de sponsor doit être l\'un des suivants: {{ value }}'
    )]
    private ?string $type_Sp = null;

    public function getType_Sp(): ?string
    {
        return $this->type_Sp;
    }
    
    // Alias for form compatibility
    public function getType(): ?string
    {
        return $this->type_Sp;
    }

    public function setType_Sp(string $type_Sp): self
    {
        $this->type_Sp = $type_Sp;
        return $this;
    }
    
    // Alias for form compatibility
    public function setType(string $type): self
    {
        return $this->setType_Sp($type);
    }

    #[ORM\Column(name: 'montantContrat', type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: 'Le montant du contrat est requis')]
    #[Assert\Positive(message: 'Le montant doit être un nombre positif')]
    #[Assert\GreaterThan(
        value: 0,
        message: 'Le montant doit être supérieur à 0'
    )]
    private ?int $montantContrat = null;

    public function getMontantContrat(): ?int
    {
        return $this->montantContrat;
    }

    public function setMontantContrat(int $montantContrat): self
    {
        $this->montantContrat = $montantContrat;
        return $this;
    }

    #[ORM\Column(name: 'dateDebut', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date de début est requise')]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date de début doit être dans le futur'
    )]
    private ?\DateTimeInterface $dateDebut = null;

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    #[ORM\Column(name: 'dateFin', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date de fin est requise')]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date de fin doit être dans le futur'
    )]
    #[Assert\Expression(
        expression: 'this.getDateDebut() <= this.getDateFin()',
        message: 'La date de fin doit être après la date de début'
    )]
    private ?\DateTimeInterface $dateFin = null;

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: GestionMatch::class, inversedBy: 'sponsors')]
    #[ORM\JoinColumn(name: 'idMatch', referencedColumnName: 'id')]
    private ?GestionMatch $gestionMatch = null;

    public function getGestionMatch(): ?GestionMatch
    {
        return $this->gestionMatch;
    }

    public function setGestionMatch(?GestionMatch $gestionMatch): self
    {
        $this->gestionMatch = $gestionMatch;
        return $this;
    }

}
