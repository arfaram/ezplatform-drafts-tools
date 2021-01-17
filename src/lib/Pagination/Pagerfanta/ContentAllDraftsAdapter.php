<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\Pagination\Pagerfanta;

use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use EzPlatform\DraftsTools\UI\Dataset\DatasetFactory;
use Pagerfanta\Adapter\AdapterInterface;

final class ContentAllDraftsAdapter implements AdapterInterface
{
    /** @var \EzPlatform\DraftsTools\Core\Repository\DraftsToolsService */
    private $draftsToolsService;

    /** @var \EzPlatform\DraftsTools\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /**
     * ContentAllDraftsAdapter constructor.
     *
     * @param \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface $draftsToolsService
     * @param \EzPlatform\DraftsTools\UI\Dataset\DatasetFactory $datasetFactory
     */
    public function __construct(
        DraftsToolsServiceInterface $draftsToolsService,
        DatasetFactory $datasetFactory
    ) {
        $this->draftsToolsService = $draftsToolsService;
        $this->datasetFactory = $datasetFactory;
    }

    /**
     * @return int
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getNbResults()
    {
        return $this->draftsToolsService->countAllContentDrafts();
    }

    /**
     * Returns an slice of the results.
     *
     * @param int $offset the offset
     * @param int $length the length
     *
     * @return array|\Traversable the slice
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function getSlice($offset, $length)
    {
        return $this->datasetFactory
            ->contentAllDraftList()
            ->load($offset, $length)
            ->getContentDrafts();
    }
}
