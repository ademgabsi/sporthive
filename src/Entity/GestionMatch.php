<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\GestionMatchRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GestionMatchRepository::class)]
#[ORM\Table(name: 'gestion_match')]
class GestionMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le nom est requis')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le type est requis')]
    #[Assert\Choice(
        choices: ['football', 'basketball', 'volleyball', 'handball'],
        message: 'Le type de match doit être l\'un des suivants: {{ value }}'
    )]
    private ?string $type = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'La description est requise')]
    #[Assert\Length(
        min: 10,
        max: 500,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotBlank(message: 'La date est requise')]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date doit être dans le futur'
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time', nullable: false)]
    #[Assert\NotBlank(message: 'L\'heure est requise')]
    private ?\DateTimeInterface $heure = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'gestionMatchs')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $qrCode = null;

    #[ORM\OneToMany(targetEntity: Sponsor::class, mappedBy: 'gestionMatch')]
    private Collection $sponsors;

    public function __construct()
    {
        $this->sponsors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): self
    {
        $this->heure = $heure;
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

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(?string $qrCode): self
    {
        $this->qrCode = $qrCode;
        return $this;
    }

    /**
     * @return Collection<int, Sponsor>
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsor $sponsor): self
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors->add($sponsor);
        }
        return $this;
    }

    public function removeSponsor(Sponsor $sponsor): self
    {
        $this->sponsors->removeElement($sponsor);
        return $this;
    }
}
