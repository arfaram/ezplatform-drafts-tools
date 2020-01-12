<?php

namespace EzPlatform\DraftsTools\Core\Repository;

use eZ\Publish\Core\Repository\Helper\RelationProcessor;
use eZ\Publish\Core\Repository\Permission\PermissionResolver as PermissionResolverInterface;
use eZ\Publish\Core\Repository\Repository as RepositoryCoreInterface;
use eZ\Publish\Core\Search\Common\BackgroundIndexer;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Search\Handler as SearchHandler;
use EzPlatform\DraftsTools\API\Repository\Repository as DraftsRepository;
use EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface as DraftPersistenceHandler;

class Repository extends RepositoryCoreInterface implements DraftsRepository
{
    /**
     * Instance of DraftTools service.
     * @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface
     */
    private $drfatToolsService;

    /** @var \EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface */
    private $draftsPersistenceHandler;

    /** @var \eZ\Publish\Core\Repository\Permission\PermissionResolver */
    private $permissionResolver;

    /**
     * Repository constructor.
     * @param \eZ\Publish\SPI\Persistence\Handler $persistenceHandler
     * @param \eZ\Publish\SPI\Search\Handler $searchHandler
     * @param \eZ\Publish\Core\Search\Common\BackgroundIndexer $backgroundIndexer
     * @param \eZ\Publish\Core\Repository\Helper\RelationProcessor $relationProcessor
     * @param \EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface $draftsPersistenceHandler
     * @param \eZ\Publish\Core\Repository\Permission\PermissionResolver $permissionResolver
     */
    public function __construct(
        PersistenceHandler $persistenceHandler,
        SearchHandler $searchHandler,
        BackgroundIndexer $backgroundIndexer,
        RelationProcessor $relationProcessor,
        DraftPersistenceHandler $draftsPersistenceHandler,
        PermissionResolverInterface $permissionResolver
    ) {
        parent::__construct($persistenceHandler, $searchHandler, $backgroundIndexer, $relationProcessor);
        $this->draftsPersistenceHandler = $draftsPersistenceHandler;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface|\EzPlatform\DraftsTools\Core\Repository\DraftsToolsService
     */
    public function getAllDraftsService()
    {
        if ($this->drfatToolsService !== null) {
            return $this->drfatToolsService;
        }

        $this->drfatToolsService = new DraftsToolsService(
            $this->draftsPersistenceHandler,
            $this->permissionResolver,
            $this->getDomainMapper()
        );

        return $this->drfatToolsService;
    }
}
