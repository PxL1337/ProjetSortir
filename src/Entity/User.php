<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('pseudo', message: 'This pseudo already exists')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 30)]
    private ?string $firstname = null;

    #[ORM\Column(length: 30)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\ManyToOne(targetEntity: Role::class, fetch: "EAGER", inversedBy: 'users')]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "id")]
    private ?Role $role = null;

    private ?UploadedFile $photoFile = null;

    #[ORM\ManyToOne(targetEntity: Campus::class, fetch: "EAGER", inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'Organizer', targetEntity: Outing::class)]
    private Collection $outingsOrganized;

    #[ORM\ManyToMany(targetEntity: Outing::class, mappedBy: 'attendees')]
    private Collection $outingsPlanned;

    public function __construct()
    {
        $this->outingsOrganized = new ArrayCollection();
        $this->outingsPlanned = new ArrayCollection();
    }

    #[ORM\Column]
    private ?bool $active = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function setPhotoFile(?UploadedFile $posterFile = null): void
    {
        $this->photoFile = $posterFile;
    }

    public function getPhotoFile(): ?UploadedFile
    {
        return $this->photoFile;
    }

    public function getRoles(): array
    {
        return [$this->role ? $this->role->getName() : 'ROLE_USER'];
    }
    public function setRoles(Collection $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOutingsOrganized(): Collection
    {
        return $this->outingsOrganized;
    }

    public function addOutingsOrganized(Outing $outingsOrganized): static
    {
        if (!$this->outingsOrganized->contains($outingsOrganized)) {
            $this->outingsOrganized->add($outingsOrganized);
            $outingsOrganized->setOrganizer($this);
        }

        return $this;
    }

    public function removeOutingsOrganized(Outing $outingsOrganized): static
    {
        if ($this->outingsOrganized->removeElement($outingsOrganized)) {
            // set the owning side to null (unless already changed)
            if ($outingsOrganized->getOrganizer() === $this) {
                $outingsOrganized->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOutingsPlanned(): Collection
    {
        return $this->outingsPlanned;
    }

    public function addOutingsPlanned(Outing $outingsPlanned): static
    {
        if (!$this->outingsPlanned->contains($outingsPlanned)) {
            $this->outingsPlanned->add($outingsPlanned);
            $outingsPlanned->addAttendee($this);
        }

        return $this;
    }

    public function removeOutingsPlanned(Outing $outingsPlanned): static
    {
        if ($this->outingsPlanned->removeElement($outingsPlanned)) {
            $outingsPlanned->removeAttendee($this);
        }

        return $this;
    }
}
