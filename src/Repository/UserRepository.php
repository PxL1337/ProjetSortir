<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUsername(string $username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :emailOrPseudo')
            ->orWhere('u.pseudo = :emailOrPseudo')
            ->setParameter('emailOrPseudo', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByUsernameWithCampus(string $username)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.campus', 'c') // Joindre la table campus
            ->addSelect('c') // Sélectionner aussi les informations du campus
            ->where('u.email = :emailOrPseudo')
            ->orWhere('u.pseudo = :emailOrPseudo')
            ->setParameter('emailOrPseudo', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllWithRoles() {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithRolesAndFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.role', 'r')
            ->addSelect('r');

        if (!empty($filters['campus'])) {
            $qb->andWhere('u.campus = :campus')
                ->setParameter('campus', $filters['campus']);
        }

        if (!empty($filters['role'])) {
            $qb->andWhere('u.role = :role')
                ->setParameter('role', $filters['role']);
        }

        if (!empty($filters['sort'])) {
            $qb->orderBy('u.'.$filters['sort']);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findBySearch(\App\Model\SearchData $searchData)
    {
            $query = $this->createQueryBuilder('u')
                ->select('u')
                ->where('u.firstname LIKE :firstname OR u.lastname LIKE :lastname')
                ->setParameter('firstname', '%' . $searchData->input . '%')
                ->setParameter('lastname', '%' . $searchData->input . '%')
                ->getQuery();

            return $query->getResult();
    }
}
