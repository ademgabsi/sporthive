<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\TerrainRepository;

#[ORM\Entity(repositoryClass: TerrainRepository::class)]
#[ORM\Table(name: 'terrain')]
class Terrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_Terrain', type: 'integer')]
    private ?int $idTerrain = null;

    #[ORM\Column(name: 'Nom', type: 'string', nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(name: 'Type_surface', type: 'string', nullable: false)]
    private ?string $typeSurface = null;

    #[ORM\Column(name: 'Localisation', type: 'string', nullable: false)]
    private ?string $localisation = null;

    #[ORM\Column(name: 'Prix', type: 'decimal', nullable: false)]
    private ?float $prix = null;

    #[ORM\Column(name: 'image_ter', type: 'string', nullable: false)]
    private ?string $imageTer = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'terrains')]
    #[ORM\JoinColumn(name: 'ID_Proprietaire', referencedColumnName: 'id')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'terrain')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getIdTerrain(): ?int { return $this->idTerrain; }
    public function setIdTerrain(int $idTerrain): self { $this->idTerrain = $idTerrain; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getTypeSurface(): ?string { return $this->typeSurface; }
    public function setTypeSurface(string $typeSurface): self { $this->typeSurface = $typeSurface; return $this; }

    public function getLocalisation(): ?string { return $this->localisation; }
    public function setLocalisation(string $localisation): self { $this->localisation = $localisation; return $this; }

    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }

    public function getImageTer(): ?string { return $this->imageTer; }
    public function setImageTer(string $imageTer): self { $this->imageTer = $imageTer; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): self { $this->utilisateur = $utilisateur; return $this; }

    public function getReservations(): Collection { return $this->reservations; }
    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->reservations->removeElement($reservation);
        return $this;
    }
}
