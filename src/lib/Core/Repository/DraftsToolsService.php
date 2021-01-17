<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\Core\Repository;

use eZ\Publish\API\Repository\PermissionService;
use eZ\Publish\API\Repository\Values\Content\ContentDraftList;
use eZ\Publish\API\Repository\Values\Content\DraftList\Item\ContentDraftListItem;
use eZ\Publish\API\Repository\Values\Content\DraftList\Item\UnauthorizedContentDraftListItem;
use eZ\Publish\Core\Repository\Mapper;
use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface;

class DraftsToolsService implements DraftsToolsServiceInterface
{
    /** @var \eZ\Publish\SPI\Persistence\Handler */
    private $persistenceHandler;

    /** @var Mapper\ContentDomainMapper */
    private $contentDomainMapper;

    /** @var \eZ\Publish\API\Repository\PermissionService */
    private $permissionService;

    /**
     * DraftsToolsService constructor.
     *
     * @param \EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface $persistenceHandler
     * @param \eZ\Publish\API\Repository\PermissionService $permissionService
     * @param Mapper\ContentDomainMapper $contentDomainMapper
     */
    public function __construct(
        HandlerInterface $persistenceHandler,
        PermissionService $permissionService,
        Mapper\ContentDomainMapper $contentDomainMapper
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->permissionService = $permissionService;
        $this->contentDomainMapper = $contentDomainMapper;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentDraftList
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function loadAllDrafts($offset = 0, $limit = 25)
    {
        //Same logic as in Content Service, see loadContentDraftList()

        $list = new ContentDraftList();
        if ($this->permissionService->hasAccess('content', 'versionread') === false) {
            return $list;
        }

        $list->totalCount = $this->countAllContentDrafts();
        if ($list->totalCount > 0) {
            $spiVersionInfoList = $this->persistenceHandler->getDraftsList($offset, $limit);
            foreach ($spiVersionInfoList as $spiVersionInfo) {
                $versionInfo = $this->contentDomainMapper->buildVersionInfoDomainObject($spiVersionInfo);
                if ($this->permissionService->canUser('content', 'versionread', $versionInfo)) {
                    $list->items[] = new ContentDraftListItem($versionInfo);
                } else {
                    $list->items[] = new UnauthorizedContentDraftListItem(
                        'content',
                        'versionread',
                        ['contentId' => $versionInfo->contentInfo->id]
                    );
                }
            }
        }

        return $list;
    }

    /**
     * @param $spiDraftList
     *
     * @return mixed
     */
    public function loadDraftsContent($spiDraftList)
    {
        return $spiDraftList;
    }

    /**
     * @return int
     */
    public function countAllContentDrafts(): int
    {
        return $this->persistenceHandler->countAllDrafts();
    }
}
