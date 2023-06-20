<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Enum\AnswerStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    //You need to make this function static, otherwise you cannot inject the Criteria-Object into the entity
    public static function createApprovedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(
                Criteria::expr()->eq('status', AnswerStatus::APPROVED->value)
            );
    }

    /**
     *
     * @return Answer[]
     * @throws QueryException
     */
    public function findAllApproved(int $max = 10): array
    {
        return $this->createQueryBuilder('answer')
            //alternative way without reusing criteria
//            ->andWhere('answer.status = :status')
//            ->setParameter('status', AnswerStatus::APPROVED->value)
            ->addCriteria(
                self::createApprovedCriteria()
            ) //reusing criteria oobject
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Answer[]
     */
    public function findMostPopular(): array
    {
        return $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedCriteria())
            ->orderBy('answer.votes', 'DESC')
            ->innerJoin('answer.question', 'question')
            ->addSelect('question') //although of the selected question-data, the repo-method will still give a result-array with answer-objects, but each answer-object will be preloaded with question-objects //select always the entity (grab everything from question), not something like question.slug ... (this is not needed)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
