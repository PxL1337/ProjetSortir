<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OutingRepository::class)]
#[ORM\Index(columns: ["date_heure_debut"], name: "outing_date_heure_debut_idx")]
#[ORM\Index(columns: ["date_limite_inscription"], name: "outing_date_limite_inscription_idx")]
#[ORM\Index(columns: ["status_id"], name: "outing_status_idx")]
#[ORM\Index(columns: ["organizer_id"], name: "outing_organizer_idx")]
#[ORM\Index(columns: ["campus_id"], name: "outing_campus_idx")]
#[ORM\Index(columns: ["place_id"], name: "outing_place_idx")]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    private ?\DateInterval $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column]
    #[Assert\Length(min: 1)]
    #[Assert\Positive(message: "Le nombre d'inscriptions maximum doit être positif")]
    private ?int $nbInscriptionMax = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $infosSortie = null;


    #[ORM\ManyToOne(fetch: "EAGER", inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'outingsOrganized')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Organizer = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'outingsPlanned')]
    private Collection $attendees;

    #[ORM\ManyToOne(fetch: "EAGER", inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(fetch: "EAGER", inversedBy: 'outings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $cancellationReason = null;

    public function __construct()
    {
        $this->attendees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): static
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?\DateInterval
    {
        return $this->duree;
    }

    public function setDuree(\DateInterval $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): static
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nbInscriptionMax;
    }

    public function setNbInscriptionMax(int $nbInscriptionMax): static
    {
        $this->nbInscriptionMax = $nbInscriptionMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): static
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->Organizer;
    }

    public function setOrganizer(?User $Organizer): static
    {
        $this->Organizer = $Organizer;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(User $attendee): static
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
        }

        return $this;
    }

    public function removeAttendee(User $attendee): static
    {
        $this->attendees->removeElement($attendee);

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

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getFormattedDuration(): string
    {
        return $this->duree->format('%H:%I');
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        // Clone la dateHeureDebut pour ne pas modifier l'original
        $heureFin = clone $this->dateHeureDebut;
        // Ajoute la durée à la dateHeureDebut pour obtenir la date et l'heure de fin
        $heureFin->add($this->duree);
        return $heureFin;
    }

    public function getRemainingPlaces(): int
    {
        return $this->nbInscriptionMax - $this->attendees->count();
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): static
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }
    public function getCity(): ?City
    {
        return $this->getPlace()->getCity();
    }
}
