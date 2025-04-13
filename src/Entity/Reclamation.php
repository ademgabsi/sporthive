<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\ReclamationRepository;
use App\Entity\Assurance;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $ID_reclamation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Description = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    private ?string $Etat = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $Montant_reclame = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $Montant_rembourse = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $Documents = null;

    #[ORM\ManyToOne(targetEntity: Assurance::class, inversedBy: 'reclamations')]
    #[ORM\JoinColumn(name: 'contrat', referencedColumnName: 'ID_contrat', nullable: false, onDelete: 'CASCADE')]
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

    public function getMontant_reclame(): ?int
    {
        return $this->Montant_reclame;
    }

    public function setMontant_reclame(?int $Montant_reclame): self
    {
        $this->Montant_reclame = $Montant_reclame;
        return $this;
    }

    public function getMontant_rembourse(): ?int
    {
        return $this->Montant_rembourse;
    }

    public function setMontant_rembourse(?int $Montant_rembourse): self
    {
        $this->Montant_rembourse = $Montant_rembourse;
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