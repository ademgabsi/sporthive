<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $mot_de_passe = null;

    public function getMot_de_passe(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMot_de_passe(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_naissance = null;

    public function getDate_naissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDate_naissance(?\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $numero_tel = null;

    public function getNumero_tel(): ?string
    {
        return $this->numero_tel;
    }

    public function setNumero_tel(string $numero_tel): self
    {
        $this->numero_tel = $numero_tel;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image = null;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id')]
    private ?Role $role = null;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Assurance::class, mappedBy: 'utilisateur')]
    private Collection $assurances;

    /**
     * @return Collection<int, Assurance>
     */
    public function getAssurances(): Collection
    {
        if (!$this->assurances instanceof Collection) {
            $this->assurances = new ArrayCollection();
        }
        return $this->assurances;
    }

    public function addAssurance(Assurance $assurance): self
    {
        if (!$this->getAssurances()->contains($assurance)) {
            $this->getAssurances()->add($assurance);
        }
        return $this;
    }

    public function removeAssurance(Assurance $assurance): self
    {
        $this->getAssurances()->removeElement($assurance);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Competition::class, mappedBy: 'utilisateur')]
    private Collection $competitions;

    /**
     * @return Collection<int, Competition>
     */
    public function getCompetitions(): Collection
    {
        if (!$this->competitions instanceof Collection) {
            $this->competitions = new ArrayCollection();
        }
        return $this->competitions;
    }

    public function addCompetition(Competition $competition): self
    {
        if (!$this->getCompetitions()->contains($competition)) {
            $this->getCompetitions()->add($competition);
        }
        return $this;
    }

    public function removeCompetition(Competition $competition): self
    {
        $this->getCompetitions()->removeElement($competition);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Equipe::class, mappedBy: 'utilisateur')]
    private Collection $equipes;

    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        if (!$this->equipes instanceof Collection) {
            $this->equipes = new ArrayCollection();
        }
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->getEquipes()->contains($equipe)) {
            $this->getEquipes()->add($equipe);
        }
        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
    {
        $this->getEquipes()->removeElement($equipe);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: GestionMatch::class, mappedBy: 'utilisateur')]
    private Collection $gestionMatchs;

    /**
     * @return Collection<int, GestionMatch>
     */
    public function getGestionMatchs(): Collection
    {
        if (!$this->gestionMatchs instanceof Collection) {
            $this->gestionMatchs = new ArrayCollection();
        }
        return $this->gestionMatchs;
    }

    public function addGestionMatch(GestionMatch $gestionMatch): self
    {
        if (!$this->getGestionMatchs()->contains($gestionMatch)) {
            $this->getGestionMatchs()->add($gestionMatch);
        }
        return $this;
    }

    public function removeGestionMatch(GestionMatch $gestionMatch): self
    {
        $this->getGestionMatchs()->removeElement($gestionMatch);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'utilisateur')]
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

    #[ORM\OneToMany(targetEntity: Terrain::class, mappedBy: 'utilisateur')]
    private Collection $terrains;

    /**
     * @return Collection<int, Terrain>
     */
    public function getTerrains(): Collection
    {
        if (!$this->terrains instanceof Collection) {
            $this->terrains = new ArrayCollection();
        }
        return $this->terrains;
    }

    public function addTerrain(Terrain $terrain): self
    {
        if (!$this->getTerrains()->contains($terrain)) {
            $this->getTerrains()->add($terrain);
        }
        return $this;
    }

    public function removeTerrain(Terrain $terrain): self
    {
        $this->getTerrains()->removeElement($terrain);
        return $this;
    }

}
