<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param array $userIds
     * @return float
     * @throws Exception
     */
    public function getPostWordCountByUsers(array $userIds): float
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
                SELECT 
                   AVG(
                       LENGTH(RTRIM(LTRIM(REPLACE(body,'  ', ' ')))) 
                       - LENGTH(REPLACE(RTRIM(LTRIM(REPLACE(body, '  ', ' '))), ' ', '')) + 1
                       ) AS average_word_count 
                FROM post WHERE user_id IN (?);
            ";

        $resultSet = $conn->executeQuery(
            $sql,
            [$userIds],
            [Connection::PARAM_INT_ARRAY],
        );

        $result = $resultSet->fetchAssociative();

        return $result['average_word_count'];
    }
}
