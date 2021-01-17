<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\UI\Dataset;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\DraftList\ContentDraftListItemInterface;
use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use EzPlatform\DraftsTools\UI\Services\DraftUserInformationService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class ContentAllDraftListDataset
{
    /** @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface */
    private $draftsToolsService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzPlatform\DraftsTools\UI\Services\DraftUserInformationService */
    private DraftUserInformationService $draftUserInformationService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface[] */
    private $data = [];

    /**
     * ContentAllDraftListDataset constructor.
     *
     * @param \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface $draftsToolsService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     * @param \EzPlatform\DraftsTools\UI\Services\DraftUserInformationService $draftUserInformationService
     */
    public function __construct(
        DraftsToolsServiceInterface $draftsToolsService,
        ContentTypeService $contentTypeService,
        ValueFactory $valueFactory,
        DraftUserInformationService $draftUserInformationService
    ) {
        $this->draftsToolsService = $draftsToolsService;
        $this->contentTypeService = $contentTypeService;
        $this->valueFactory = $valueFactory;
        $this->draftUserInformationService = $draftUserInformationService;
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \EzPlatform\DraftsTools\UI\Dataset\ContentAllDraftListDataset
     */
    public function load(int $offset = 0, int $limit = 0): self
    {
        // Same like in EzSystems\EzPlatformAdminUi\UI\Dataset\ContentDraftListDataset, load(), to avoid access drafts for current user. We add later the owner and version creator as additional information
        $contentDraftListItems = $this->draftsToolsService->loadAllDrafts($offset, $limit)->items;

        $contentTypes = $contentTypeIds = [];
        foreach ($contentDraftListItems as $contentDraftListItem) {
            if ($contentDraftListItem->hasVersionInfo()) {
                $contentTypeIds[] = $contentDraftListItem->getVersionInfo()->getContentInfo()->contentTypeId;
            }
        }

        if (!empty($contentTypeIds)) {
            $contentTypes = $this->contentTypeService->loadContentTypeList(array_unique($contentTypeIds));
        }

        $this->data = array_map(
            function (ContentDraftListItemInterface $contentDraftListItem) use ($contentTypes) {
                if ($contentDraftListItem->hasVersionInfo()) {
                    $versionInfo = $contentDraftListItem->getVersionInfo();
                    $contentType = $contentTypes[$versionInfo->getContentInfo()->contentTypeId];
                    $contentDraft = $this->valueFactory->createContentDraft($contentDraftListItem, $contentType);
                    //additional owner and creator information
                    return $this->draftUserInformationService->getDraftUserInformation($contentDraft);
                }

                return $this->valueFactory->createUnauthorizedContentDraft($contentDraftListItem);
            },
            $contentDraftListItems
        );

        return $this;
    }

    /**
     * @return \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraftInterface[]
     */
    public function getContentDrafts(): array
    {
        return $this->data;
    }
}
