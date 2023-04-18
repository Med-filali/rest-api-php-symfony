<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * A simple query to have paginated elements
     * to go further it could be interesting to go or even libraries like KnpPaginator
     * @param integer|null $page
     * @param integer|null $limit
     * @return array
     */
    public function findAllWithPagination(?int $page, ?int $limit): array {
        if( $limit === null || $page === null ){
            return $this->findAll();
        }
        return $this->resultPagination($page,$limit);
    }

    /**
     * returns the elements for a given page
     * @param integer $page
     * @param integer $limit
     * @return array
     */
    public function resultPagination(int $page, ?int $limit): array {
        $qb = $this->createQueryBuilder('b')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }


}
