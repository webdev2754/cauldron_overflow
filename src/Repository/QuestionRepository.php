<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

     /**
      * @return Question[] Returns an array of Question objects
      */
    public function findAllAskedOrderedByNewest()
    {
        return $this->addIsAskedQueryBuilder()
            ->orderBy('q.askedAt', 'DESC')
            /**
             * To fix the N+1 problem, we need to add a join. And this is where things get interesting. In the database,
             * we need to join from question to question_tag... and then join from question_tag over to tag. So we actually need two joins.
             * But in Doctrine, we get to pretend like that join table doesn't exist:
             * Doctrine wants us to pretend that there is a direct relationship from question to tag.
             * What I mean is, to do the join, all we need is ->leftJoin() - because we want to get the many tags for this question - q.tags, tag.
             * That's it. We reference the tags property on question... and let Doctrine figure out how to join over to that.
             * The second argument - tag - becomes the alias to the data on the tag table. We need that to select its data: addSelect('tag').
             * So... yup! Joining across a ManyToMany relationship is no different than joining across a ManyToOne relationship:
             * you reference the relation property (q.tags) and Doctrine does the heavy lifting.
             */
            ->leftJoin('q.tags', 'tag')
            ->addSelect('tag')
            ->getQuery()
            ->getResult()
        ;
    }

    private function addIsAskedQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('q.askedAt IS NOT NULL');
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('q');
    }

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
