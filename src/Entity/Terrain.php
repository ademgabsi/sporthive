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
    #[ORM\Column(type: 'integer')]
    private ?int $id_Terrain = null;

    public function getId_Terrain(): ?int
    {
        return $this->id_Terrain;
    }

    public function setId_Terrain(int $id_Terrain): self
    {
        $this->id_Terrain = $id_Terrain;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Nom = null;

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Type_surface = null;

    public function getType_surface(): ?string
    {
        return $this->Type_surface;
    }

    public function setType_surface(string $Type_surface): self
    {
        $this->Type_surface = $Type_surface;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Localisation = null;

    public function getLocalisation(): ?string
    {
        return $this->Localisation;
    }

    public function setLocalisation(string $Localisation): self
    {
        $this->Localisation = $Localisation;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $Prix = null;

    public function getPrix(): ?float
    {
        return $this->Prix;
    }

    public function setPrix(float $Prix): self
    {
        $this->Prix = $Prix;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'terrains')]
    #[ORM\JoinColumn(name: 'ID_Proprietaire', referencedColumnName: 'id')]
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $image_ter = null;

    public function getImage_ter(): ?string
    {
        return $this->image_ter;
    }

    public function setImage_ter(string $image_ter): self
    {
        $this->image_ter = $image_ter;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'terrain')]
    private Collection $reservations;

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        if (!$this->reservations instanceof Collection) {
            $this->reservations = new ArrayCollection();
        }
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->getReservations()->contains($reservation)) {
            $this->getReservations()->add($reservation);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->getReservations()->removeElement($reservation);
        return $this;
    }

}
