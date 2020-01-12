<?php

namespace EzPlatform\DraftsTools\UI\Dataset;

use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use eZ\Publish\API\Repository\ContentTypeService;
use EzPlatform\DraftsTools\UI\Value\AllDraftsValueFactory;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class DatasetFactory
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface */
    private $draftsToolsService;

    /** @var \EzPlatform\DraftsTools\UI\Value\AllDraftsValueFactory */
    private $allDraftsValueFactory;

    /**
     * DatasetFactory constructor.
     * @param \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface $draftsToolsService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory $valueFactory
     */
    public function __construct(
        DraftsToolsServiceInterface $draftsToolsService,
        ContentTypeService $contentTypeService,
        ValueFactory $valueFactory,
        AllDraftsValueFactory $allDraftsValueFactory
    ) {
        $this->draftsToolsService = $draftsToolsService;
        $this->contentTypeService = $contentTypeService;
        $this->valueFactory = $valueFactory;
        $this->allDraftsValueFactory = $allDraftsValueFactory;
    }

    /**
     * @return \EzPlatform\DraftsTools\UI\Dataset\ContentAllDraftListDataset
     */
    public function contentAllDraftList(): ContentAllDraftListDataset
    {
        return new ContentAllDraftListDataset(
            $this->draftsToolsService,
            $this->contentTypeService,
            $this->valueFactory,
            $this->allDraftsValueFactory
        );
    }
}
