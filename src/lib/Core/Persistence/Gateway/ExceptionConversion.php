<?php

namespace EzPlatform\DraftsTools\Core\Persistence\Gateway;

use Doctrine\DBAL\DBALException;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use EzPlatform\DraftsTools\Core\Persistence\DraftsGateway;
use PDOException;

class ExceptionConversion extends DraftsGateway
{
    /** @var \EzPlatform\DraftsTools\Core\Persistence\DraftsGateway */
    private $innerGateway;

    /**
     * ExceptionConversion constructor.
     * @param \EzPlatform\DraftsTools\Core\Persistence\DraftsGateway $innerGateway
     */
    public function __construct(
        DraftsGateway $innerGateway
    ) {
        $this->innerGateway = $innerGateway;
    }

    /**
     * @param $offset
     * @param $limit
     * @param int $status
     * @return mixed
     */
    public function getDraftsList($offset, $limit, $status = VersionInfo::STATUS_DRAFT)
    {
        try {
            return $this->innerGateway->getDraftsList($offset, $limit, $status);
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * @return mixed
     */
    public function countAllDrafts()
    {
        try {
            return $this->innerGateway->countAllDrafts();
        } catch (DBALException | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }
}
