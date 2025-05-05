<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Repository\ReclamationRepository;
use App\Entity\Assurance;
<<<<<<< HEAD
use App\Entity\Utilisateur;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;
=======
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> gestionMatch

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID_reclamation', type: 'integer')]
    private ?int $ID_reclamation = null;

    #[ORM\Column(name: 'Date', type: Types::DATE_MUTABLE, nullable: true)]
<<<<<<< HEAD
    #[Assert\NotNull(message: 'La date ne peut pas être vide')]
    #[Assert\Type(\DateTimeInterface::class, message: 'La date n\'est pas valide')]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(name: 'Description', type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank(message: 'La description ne peut pas être vide')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'La description doit comporter au moins {{ limit }} caractères',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères',
    )]
    #[CustomAssert\NoProfanity]
    private ?string $Description = null;

    #[ORM\Column(name: 'Etat', type: Types::STRING, length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'L\'état ne peut pas être vide')]
    #[Assert\Choice(
        choices: ['En attente', 'Active', 'Expirée'],
        message: 'Choisissez un état valide',
    )]
    private ?string $Etat = null;

    #[ORM\Column(name: 'Montant_reclame', type: Types::INTEGER, nullable: true)]
    #[Assert\NotNull(message: 'Le montant réclamé ne peut pas être vide')]
    #[Assert\Type(type: 'integer', message: 'Le montant réclamé doit être un nombre entier')]
    #[Assert\GreaterThan(
        value: 0,
        message: 'Le montant réclamé doit être supérieur à {{ compared_value }}',
    )]
    private ?int $Montant_reclame = null;
=======
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
>>>>>>> gestionMatch

    #[ORM\Column(name: 'Montant_rembourse', type: Types::INTEGER, nullable: true)]
    #[Assert\Type(type: 'integer', message: 'Le montant remboursé doit être un nombre entier')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'Le montant remboursé doit être supérieur ou égal à {{ compared_value }}',
    )]
    private ?int $Montant_rembourse = null;

    #[ORM\Column(name: 'Documents', type: Types::STRING, length: 255, nullable: true)]
<<<<<<< HEAD
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le chemin du document ne peut pas dépasser {{ limit }} caractères',
=======
    #[Assert\NotBlank(message: 'Les documents sont requis')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom des documents ne peut pas dépasser {{ limit }} caractères'
>>>>>>> gestionMatch
    )]
    private ?string $Documents = null;

    #[ORM\ManyToOne(targetEntity: Assurance::class, inversedBy: 'reclamations')]
    #[ORM\JoinColumn(name: 'contrat', referencedColumnName: 'ID_contrat', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Veuillez sélectionner un contrat d\'assurance')]
    private ?Assurance $assurance = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Veuillez sélectionner un utilisateur')]
    private ?Utilisateur $utilisateur = null;

    public function getId(): ?int
    {
        return $this->ID_reclamation;
    }

    public function getID_reclamation(): ?int
    {
        return $this->ID_reclamation;
    }

    public function setID_reclamation(int $ID_reclamation): self
    {
        $this->ID_reclamation = $ID_reclamation;
        return $this;
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

    public function getMontantReclame(): ?int
    {
        return $this->Montant_reclame;
    }

    public function setMontantReclame(?int $Montant_reclame): self
    {
        $this->Montant_reclame = $Montant_reclame;
        return $this;
    }

    public function getMontantRembourse(): ?int
    {
        return $this->Montant_rembourse;
    }

    public function setMontantRembourse(?int $Montant_rembourse): self
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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }
}