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

    public function findAllWithDetails(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.Organizer', 'u')
            ->addSelect('u')
            ->innerJoin('o.status', 's')
            ->addSelect('s')
            ->innerJoin('o.campus', 'c')
            ->addSelect('c')
            ->innerJoin('o.place', 'p')
            ->addSelect('p');

        return $qb->getQuery()->getResult();
    }

    public function findWithFilters(SearchData $data) {
        $query = $this
            ->createQueryBuilder('o')
            ->select('o', 'att', 'org', 'sta', 'cam', 'pla')  // Include all related entities
            ->leftJoin('o.attendees', 'att')
            ->join('o.Organizer', 'org')
            ->join('o.status', 'sta')
            ->join('o.campus', 'cam')
            ->join('o.place', 'pla')  // Join other entities here as needed
            ->orderBy('o.dateHeureDebut', 'DESC');

        // Exclude outings that happened more than a month ago, unless the user is an admin
        $oneMonthAgo = (new \DateTime())->modify('-1 month');
        if (!in_array('ROLE_ADMIN', $data->getUser()->getRoles()) || !$data->pastMonth) {
            $query = $query->andWhere('o.dateHeureDebut >= :one_month_ago')
                ->setParameter('one_month_ago', $oneMonthAgo);
        }

        if (!empty($data->campus)) {
            $query->andWhere('o.campus IN (:campus)')
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

        if ($data->organizer) {
            $query->andWhere('org = :user')
                ->setParameter('user', $data->getUser());
        }

        if ($data->planned) {
            $query->andWhere(':user MEMBER OF o.attendees')
                ->setParameter('user', $data->getUser());
        }

        if ($data->notRegistered) {
            $query->andWhere(':user NOT MEMBER OF o.attendees')
                ->setParameter('user', $data->getUser());
        }

        if ($data->past) {
            $query->andWhere('o.dateHeureDebut < :now')
                ->setParameter('now', new \DateTime());
        }

        return $query->getQuery()->getResult();
    }

}

