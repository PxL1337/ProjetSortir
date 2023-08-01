<?php

namespace App\Data;

use App\Entity\Campus;
use DateTimeInterface;

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



}