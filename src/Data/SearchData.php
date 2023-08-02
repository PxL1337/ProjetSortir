<?php

namespace App\Data;

use App\Entity\Campus;
use DateTimeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchData
{
    /**
     * @var string
     */
    public ?string $q = '';



    /**
     * @var Campus[]
     */
    public array $campus = [];

    /**
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $dateMin = null;

    /**
     * @var DateTimeInterface|null
     */
    public ?DateTimeInterface $dateMax = null;

    public bool $organizer = false;
    public bool $planned = false;
    public bool $notRegistered = false;
    public bool $past = false;

    private ?UserInterface $user = null;

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

}