<?php

namespace App\Service;

use App\Entity\Outing;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;

class OutingStatusUpdater
{
    private EntityManagerInterface $entityManager;
    private StatusRepository $statusRepository;
    private OutingRepository $outingRepository;
    private array $statuses = [];

    public function __construct(EntityManagerInterface $entityManager, StatusRepository $statusRepository, OutingRepository $outingRepository)
    {
        $this->entityManager = $entityManager;
        $this->statusRepository = $statusRepository;
        $this->outingRepository = $outingRepository;
        $this->initializeStatuses();
    }

    private function initializeStatuses(): void
    {
        $statuses = $this->statusRepository->findAll();
        foreach ($statuses as $status) {
            $this->statuses[$status->getLibelle()] = $status;
        }
    }

    public function updateStatus(Outing $outing): void
    {
        $now = new \DateTime();
        if ($outing->getStatus()->getLibelle() === 'Ouverte' && $outing->getDateLimiteInscription() <= $now) {
            $outing->setStatus($this->statuses['Clôturée']);
        } elseif ($outing->getStatus()->getLibelle() === 'Clôturée' && $outing->getDateHeureDebut() <= $now) {
            $outing->setStatus($this->statuses['Activité en cours']);
        } elseif ($outing->getStatus()->getLibelle() === 'Activité en cours' && $now >= $this->getOutingEndTime($outing)) {
            $outing->setStatus($this->statuses['Passée']);
        }
    }

    private function getOutingEndTime(Outing $outing): \DateTimeInterface
    {
        $endDateTime = clone $outing->getDateHeureDebut();
        $endDateTime->add($outing->getDuree());
        return $endDateTime;
    }

    public function updateAllOutingsStatuses(): void
    {
        $outings = $this->outingRepository->findAllWithDetails();
        foreach ($outings as $outing) {
            $this->updateStatus($outing);
        }
        $this->entityManager->flush();
    }
}
