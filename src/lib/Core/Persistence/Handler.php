<?php

namespace EzPlatform\DraftsTools\Core\Persistence;

use eZ\Publish\Core\Persistence\Legacy\Content\Gateway;
use eZ\Publish\Core\Persistence\Legacy\Content\Mapper;
use EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface;

class Handler implements HandlerInterface
{
    /** @var \EzPlatform\DraftsTools\Core\Persistence\DraftsGateway */
    private $draftsGateway;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Gateway */
    private $contentGateway;

    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Mapper */
    private $mapper;

    /**
     * Handler constructor.
     * @param \EzPlatform\DraftsTools\Core\Persistence\DraftsGateway $draftsGateway
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\Gateway $contentGateway
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\Mapper $mapper
     */
    public function __construct(
        DraftsGateway $draftsGateway,
        Gateway $contentGateway,
        Mapper $mapper
    ) {
        $this->draftsGateway = $draftsGateway;
        $this->contentGateway = $contentGateway;
        $this->mapper = $mapper;
    }

    /**
     * @param $offset
     * @param $limit
     * @return \eZ\Publish\SPI\Persistence\Content\VersionInfo[]
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function getDraftsList($offset, $limit)
    {
        $rows = $this->draftsGateway->getDraftsList($offset, $limit);

        $idVersionPairs = array_map(
            static function (array $row): array {
                return [
                    'id' => $row['ezcontentobject_version_contentobject_id'],
                    'version' => $row['ezcontentobject_version_version'],
                ];
            },
            $rows
        );
        $nameRows = $this->contentGateway->loadVersionedNameData($idVersionPairs);

        return $this->mapper->extractVersionInfoListFromRows($rows, $nameRows);
    }

    /**
     * @return mixed
     */
    public function countAllDrafts()
    {
        return $this->draftsGateway->countAllDrafts();
    }
}
