<?php

namespace EzPlatform\DraftsTools\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder as DoctrineQueryBuilder;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use EzPlatform\DraftsTools\Core\Persistence\DraftsGateway;

class DoctrineDatabase extends DraftsGateway
{
    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * DoctrineDatabase constructor.
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    /**
     * @param $offset
     * @param $limit
     * @param int $status
     * @return mixed[]
     */
    public function getDraftsList($offset, $limit, $status = VersionInfo::STATUS_DRAFT)
    {
        $query = $this->createVersionInfoFindQueryBuilder();
        $expr = $query->expr();
        $query->where(
            $expr->andX(
                $expr->eq('v.status', ':status')
            )
        )
            ->setFirstResult($offset)
            ->setParameter(':status', $status, ParameterType::INTEGER);

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        $query->orderBy('v.modified', 'DESC');

        return $query->execute()->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function createVersionInfoFindQueryBuilder(): DoctrineQueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();

        $query
            ->select(
                'v.id AS ezcontentobject_version_id',
                'v.version AS ezcontentobject_version_version',
                'v.modified AS ezcontentobject_version_modified',
                'v.creator_id AS ezcontentobject_version_creator_id',
                'v.created AS ezcontentobject_version_created',
                'v.status AS ezcontentobject_version_status',
                'v.contentobject_id AS ezcontentobject_version_contentobject_id',
                'v.initial_language_id AS ezcontentobject_version_initial_language_id',
                'v.language_mask AS ezcontentobject_version_language_mask',
                // Content main location
                't.main_node_id AS ezcontentobject_tree_main_node_id',
                // Content object
                'c.id AS ezcontentobject_id',
                'c.contentclass_id AS ezcontentobject_contentclass_id',
                'c.section_id AS ezcontentobject_section_id',
                'c.owner_id AS ezcontentobject_owner_id',
                'c.remote_id AS ezcontentobject_remote_id',
                'c.current_version AS ezcontentobject_current_version',
                'c.initial_language_id AS ezcontentobject_initial_language_id',
                'c.modified AS ezcontentobject_modified',
                'c.published AS ezcontentobject_published',
                'c.status AS ezcontentobject_status',
                'c.name AS ezcontentobject_name',
                'c.language_mask AS ezcontentobject_language_mask',
                'c.is_hidden AS ezcontentobject_is_hidden'
            )
            ->from('ezcontentobject_version', 'v')
            ->innerJoin(
                'v',
                'ezcontentobject',
                'c',
                $expr->eq('c.id', 'v.contentobject_id')
            )
            ->leftJoin(
                'v',
                'ezcontentobject_tree',
                't',
                $expr->andX(
                    $expr->eq('t.contentobject_id', 'v.contentobject_id'),
                    $expr->eq('t.main_node_id', 't.node_id')
                )
            );

        return $query;
    }

    /**
     * @param int $status
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function countAllDrafts(int $status = VersionInfo::STATUS_DRAFT): int
    {
        $platform = $this->connection->getDatabasePlatform();
        $query = $this->connection->createQueryBuilder();
        $expr = $query->expr();

        $query
            ->select($platform->getCountExpression('v.id'))
            ->from('ezcontentobject_version', 'v')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('v.status', ':status')
                )
            )
            ->setParameter(':status', $status, \PDO::PARAM_INT);

        return (int) $query->execute()->fetchColumn();
    }
}
