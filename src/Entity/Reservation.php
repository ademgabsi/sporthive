<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Terrain;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraints as Assert; // ✅ Ajout pour les contraintes

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $ID_Reservation = null;

    public function getID_Reservation(): ?int
    {
        return $this->ID_Reservation;
    }

    public function setID_Reservation(int $ID_Reservation): self
    {
        $this->ID_Reservation = $ID_Reservation;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
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

    #[ORM\ManyToOne(targetEntity: Terrain::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'id_Terrain', referencedColumnName: 'id_Terrain')]
    private ?Terrain $terrain = null;

    public function getTerrain(): ?Terrain
    {
        return $this->terrain;
    }

    public function setTerrain(?Terrain $terrain): self
    {
        $this->terrain = $terrain;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: 'La date et l\'heure sont obligatoires.')]
    #[Assert\GreaterThanOrEqual('today', message: 'La date doit être supérieure ou égale à aujourd\'hui.')] // ✅ Contrainte de validation ajoutée
    private ?\DateTimeInterface $Date_Heure = null;

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->Date_Heure;
    }

    public function setDateHeure(?\DateTimeInterface $DateHeure): self
    {
        $this->Date_Heure = $DateHeure;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(message: 'La durée est obligatoire.')]
    #[Assert\Regex(
        pattern: '/^\d{1,2}h(\d{1,2})?$/',
        message: 'Le format de la durée est invalide. Exemple : 1h, 1h30, 2h15...'
    )]
    private ?string $Duree = null;
    
    public function getDuree(): ?string
    {
        return $this->Duree;
    }

    public function setDuree(?string $Duree): self
    {
        $this->Duree = $Duree;
        return $this;
    }
}
