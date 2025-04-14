<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReclamationRepository;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $ID_reclamation = null;

    public function getID_reclamation(): ?int
    {
        return $this->ID_reclamation;
    }

    public function setID_reclamation(int $ID_reclamation): self
    {
        $this->ID_reclamation = $ID_reclamation;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $Date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(?\DateTimeInterface $Date): self
    {
        $this->Date = $Date;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $Description = null;

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): self
    {
        $this->Description = $Description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Etat = null;

    public function getEtat(): ?string
    {
        return $this->Etat;
    }

    public function setEtat(?string $Etat): self
    {
        $this->Etat = $Etat;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $Montant_reclame = null;

    public function getMontant_reclame(): ?float
    {
        return $this->Montant_reclame;
    }

    public function setMontant_reclame(?float $Montant_reclame): self
    {
        $this->Montant_reclame = $Montant_reclame;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $Montant_rembourse = null;

    public function getMontant_rembourse(): ?float
    {
        return $this->Montant_rembourse;
    }

    public function setMontant_rembourse(?float $Montant_rembourse): self
    {
        $this->Montant_rembourse = $Montant_rembourse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $Documents = null;

    public function getDocuments(): ?string
    {
        return $this->Documents;
    }

    public function setDocuments(?string $Documents): self
    {
        $this->Documents = $Documents;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Assurance::class, inversedBy: 'reclamations')]
    #[ORM\JoinColumn(name: 'ID_contrat', referencedColumnName: 'ID_contrat')]
    private ?Assurance $assurance = null;

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
