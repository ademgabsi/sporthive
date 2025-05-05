<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(name: 'reset_token', type: 'string', length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(name: 'reset_token_expires_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    #[ORM\Column(name: 'reset_code', type: 'string', length: 6, nullable: true)]
    private ?string $resetCode = null;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }

    public function getResetCode(): ?string
    {
        return $this->resetCode;
    }

    public function setResetCode(?string $resetCode): self
    {
        $this->resetCode = $resetCode;
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
    
    // CamelCase accessors for better Symfony form compatibility
    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(?\DateTimeInterface $date_naissance): self
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
    
    // CamelCase accessors for better Symfony form compatibility
    public function getNumeroTel(): ?string
    {
        return $this->numero_tel;
    }

    public function setNumeroTel(string $numero_tel): self
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

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(name: 'google_auth_secret', type: 'string', length: 255, nullable: true)]
    private $googleAuthSecret;

    #[ORM\Column(name: 'is_two_factor_enabled', type: 'boolean')]
    private $isTwoFactorEnabled = false;

    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getGoogleAuthSecret(): ?string
    {
        return $this->googleAuthSecret;
    }

    public function setGoogleAuthSecret(?string $googleAuthSecret): self
    {
        $this->googleAuthSecret = $googleAuthSecret;
        return $this;
    }

    public function getIsTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setIsTwoFactorEnabled(bool $isTwoFactorEnabled): self
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Assurance::class)]
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: AssuranceUtilisateur::class)]
    private Collection $assurancesSouscrites;

    public function __construct()
    {
        $this->assurancesSouscrites = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    public function getAssurancesSouscrites(): Collection
    {
        return $this->assurancesSouscrites;
    }

    public function addAssuranceSouscrite(AssuranceUtilisateur $assuranceUtilisateur): self
    {
        if (!$this->assurancesSouscrites->contains($assuranceUtilisateur)) {
            $this->assurancesSouscrites[] = $assuranceUtilisateur;
            $assuranceUtilisateur->setUtilisateur($this);
        }
        return $this;
    }

    public function removeAssuranceSouscrite(AssuranceUtilisateur $assuranceUtilisateur): self
    {
        if ($this->assurancesSouscrites->removeElement($assuranceUtilisateur)) {
            if ($assuranceUtilisateur->getUtilisateur() === $this) {
                $assuranceUtilisateur->setUtilisateur(null);
            }
        }
        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Competition::class)]
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Equipe::class)]
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: GestionMatch::class)]
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reservation::class)]
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

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Terrain::class)]
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

    /**
     * The public representation of the user
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        
        // Add role based on the relationship
        if ($this->role && $this->role->getNom() === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        }
        
        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}
