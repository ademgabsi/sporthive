<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\JoueurRepository;

#[ORM\Entity(repositoryClass: JoueurRepository::class)]
#[ORM\Table(name: 'joueur')]
class Joueur
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
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
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
    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "La date de naissance est obligatoire.")]
    #[Assert\LessThan("today", message: "La date de naissance doit être dans le passé.")]
    private ?\DateTimeInterface $dateNaissance = null; // Changé en camelCase

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }
    
    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }
    
    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'joueurs')]
    #[ORM\JoinColumn(name: 'equipe', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "L'équipe est obligatoire.")]
    private ?Equipe $equipe = null;

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): self
    {
        $this->equipe = $equipe;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $photo ;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

}
