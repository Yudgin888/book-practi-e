<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conference|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conference|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConferenceRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    /**
     * @param Conference $entity
     * @param bool $flush
     * @return void
     */
    public function add(Conference $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param Conference $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Conference $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Conference[]
     */
    public function findAll(): array
    {
        return $this->findBy([], ['year' => 'ASC', 'city' => 'ASC']);
    }

    /**
     * @param string $slug
     * @return array
     * @throws NonUniqueResultException
     */
    public function findBySlug(string $slug): array
    {
        //return $this->findBy(['slug' => $slug], ['year' => 'ASC', 'city' => 'ASC']);
        return $this->createQueryBuilder('c')
            ->andWhere('c.slug = :val')
            ->setParameter('val', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
