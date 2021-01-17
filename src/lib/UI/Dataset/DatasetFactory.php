<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\UI\Dataset;

use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use eZ\Publish\API\Repository\ContentTypeService;
use EzPlatform\DraftsTools\UI\Services\DraftUserInformationService;
use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory;

class DatasetFactory
{
    /** @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface */
    private $draftsToolsService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory */
    private $valueFactory;

    /** @var \EzPlatform\DraftsTools\UI\Services\DraftUserInformationService */
    private DraftUserInformationService $draftUserInformationService;

    /**
     * DatasetFactory constructor.
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
     * @return \EzPlatform\DraftsTools\UI\Dataset\ContentAllDraftListDataset
     */
    public function contentAllDraftList(): ContentAllDraftListDataset
    {
        return new ContentAllDraftListDataset(
            $this->draftsToolsService,
            $this->contentTypeService,
            $this->valueFactory,
            $this->draftUserInformationService
        );
    }
}
