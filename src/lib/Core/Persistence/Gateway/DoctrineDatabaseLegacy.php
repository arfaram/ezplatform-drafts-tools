<?php

namespace EzPlatform\DraftsTools\Core\Persistence\Gateway;

use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use eZ\Publish\Core\Persistence\Legacy\Content\Gateway\DoctrineDatabase\QueryBuilder;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use EzPlatform\DraftsTools\Core\Persistence\DraftsGateway;

/**
 * @deprecated use instead DoctrineDatabase.php which use Doctrine\DBAL\Query\QueryBuilder
 *
 * Class DoctrineDatabaseLegacy
 */
class DoctrineDatabaseLegacy extends DraftsGateway
{
    /** @var DatabaseHandler */
    protected $dbHandler;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Gateway\DoctrineDatabase\QueryBuilder */
    private $queryBuilder;

    public function __construct(
        DatabaseHandler $dbHandler,
        QueryBuilder $queryBuilder
    ) {
        $this->dbHandler = $dbHandler;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @deprecated use instead DoctrineDatabase->getDraftsList
     *
     * @param $page
     * @param $limit
     * @param int $status
     * @return \string[][]
     */
    public function getDraftsList($offset, $limit, $status = VersionInfo::STATUS_DRAFT)
    {
        $query = $this->queryBuilder->createVersionInfoFindQuery();
        $query->where(
            $query->expr->eq(
                    $this->dbHandler->quoteColumn('status', 'ezcontentobject_version'),
                    $query->bindValue($status, null, \PDO::PARAM_INT)
                )
        );

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $this->listVersionsHelper($query);
    }

    /**
     * Helper for {@see listVersions()} and {@see listVersionsForUser()} that filters duplicates
     * that are the result of the cartesian product performed by createVersionInfoFindQuery().
     *
     * @param \eZ\Publish\Core\Persistence\Database\SelectQuery $query
     *
     * @return string[][]
     */
    private function listVersionsHelper(SelectQuery $query)
    {
        $query->orderBy(
            $this->dbHandler->quoteColumn('modified', 'ezcontentobject_version'),
            SelectQuery::DESC
        );

        $statement = $query->prepare();
        $statement->execute();

        $results = [];
        $previousId = null;
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if ($row['ezcontentobject_version_id'] == $previousId) {
                continue;
            }

            $previousId = $row['ezcontentobject_version_id'];
            $results[] = $row;
        }

        return $results;
    }
}
