<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\ReclamationRepository;
use App\Entity\Assurance;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_reclamation', type: 'integer')]
    private ?int $ID_reclamation = null;

    #[ORM\Column(name: 'Date', type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotBlank(message: 'La date est requise')]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date doit être dans le futur'
    )]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(name: 'Description', type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'La description est requise')]
    #[Assert\Length(
        min: 20,
        max: 1000,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $Description = null;

    #[ORM\Column(name: 'Etat', type: Types::STRING, length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'L\'état est requis')]
    #[Assert\Choice(
        choices: ['Active', 'En attente', 'En cours'],
        message: 'L\'état doit être l\'un des suivants: {{ value }}'
    )]
    private ?string $Etat = null;

    #[ORM\Column(name: 'montant_reclame', type: Types::INTEGER, nullable: true)]
    #[Assert\NotBlank(message: 'Le montant réclamé est requis')]
    #[Assert\Positive(message: 'Le montant doit être un nombre positif')]
    #[Assert\GreaterThan(
        value: 0,
        message: 'Le montant doit être supérieur à 0'
    )]
    private ?int $Montantreclame = null;

    #[ORM\Column(name: 'montant_rembourse', type: Types::INTEGER, nullable: true)]
    private ?int $Montantrembourse = null;

    #[ORM\Column(name: 'Documents', type: Types::STRING, length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Les documents sont requis')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom des documents ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $Documents = null;

    #[ORM\ManyToOne(targetEntity: Assurance::class, inversedBy: 'reclamations')]
    #[ORM\JoinColumn(name: 'ID_contrat', referencedColumnName: 'ID_contrat', nullable: false, onDelete: 'CASCADE')]
    private ?Assurance $assurance = null;

    // Getters and Setters
    public function getID_reclamation(): ?int
    {
        return $this->ID_reclamation;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): self
    {
        $this->Etat = $Etat;
        return $this;
    }

    public function getMontantreclame(): ?int
    {
        return $this->Montantreclame;
    }

    public function setMontantreclame(?int $Montantreclame): self
    {
        $this->Montantreclame = $Montantreclame;
        return $this;
    }

    public function getMontantrembourse(): ?int
    {
        return $this->Montantrembourse;
    }

    public function setMontantrembourse(?int $Montantrembourse): self
    {
        $this->Montantrembourse = $Montantrembourse;
        return $this;
    }

    public function getDocuments(): ?string
    {
        return $this->Documents;
    }

    public function setDocuments(?string $Documents): self
    {
        $this->Documents = $Documents;
        return $this;
    }

    public function getAssurance(): ?Assurance
    {
        return $this->assurance;
    }

    public function setAssurance(?Assurance $assurance): self
    {
        $this->assurance = $assurance;
        return $this;
    }
}