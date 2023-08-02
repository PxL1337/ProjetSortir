<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Outing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 *
 * @method Outing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Outing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Outing[]    findAll()
 * @method Outing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findAllWithAttendees(): array
    {
        $qb = $this->createQueryBuilder('o');

        $qb->select('o', 'att', 'org', 'sta', 'cam', 'pla')
            ->leftJoin('o.attendees', 'att')
            ->join('o.Organizer', 'org')
            ->join('o.status', 'sta')
            ->join('o.campus', 'cam')
            ->join('o.place', 'pla')
            ->orderBy('o.dateHeureDebut', 'DESC');

        $query = $qb->getQuery();

        // returns an array of Outing objects
        return $query->getResult();
    }

    public function findWithFilters(SearchData $data) {
        $query = $this
            ->createQueryBuilder('o')
            ->addSelect('o')
            ->orderBy('o.dateHeureDebut', 'DESC')
        ;
        if (!empty($data->campus)) {
            $query->andWhere('o.campus = :campus')
                ->setParameter('campus', $data->campus);
        }
        if (!empty($data->q)){
            $query=$query->andWhere('o.nom LIKE :q')
                ->setParameter('q', "%{$data->q}%");
        }
        if (!empty($data->dateMin)){
            $query = $query->andWhere('o.dateHeureDebut >= :dateMin')
                ->setParameter('dateMin', $data->dateMin);
        }
        if (!empty($data->dateMax)){
            $query = $query->andWhere('o.dateHeureDebut <= :dateMax')
                ->setParameter('dateMax', $data->dateMax);
        }

        return $query->getQuery()->getResult();
    }
}

