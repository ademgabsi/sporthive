<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RoleRepository;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: 'roles')]
class Role
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

    #[ORM\OneToMany(targetEntity: Utilisateur::class, mappedBy: 'role')]
    private Collection $utilisateurs;

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        if (!$this->utilisateurs instanceof Collection) {
            $this->utilisateurs = new ArrayCollection();
        }
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->getUtilisateurs()->contains($utilisateur)) {
            $this->getUtilisateurs()->add($utilisateur);
        }
        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        $this->getUtilisateurs()->removeElement($utilisateur);
        return $this;
    }

}
