<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\Core\Repository;

use eZ\Publish\API\Repository\LanguageResolver;
use eZ\Publish\API\Repository\PermissionService;
use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use eZ\Publish\Core\Repository\Helper\RelationProcessor;
use eZ\Publish\Core\Repository\Mapper;
use eZ\Publish\Core\Repository\Permission\LimitationService;
use eZ\Publish\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface;
use eZ\Publish\Core\Repository\Repository as RepositoryCoreInterface;
use eZ\Publish\Core\Repository\User\PasswordHashServiceInterface;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\Core\Search\Common\BackgroundIndexer;
use eZ\Publish\SPI\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use eZ\Publish\SPI\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use eZ\Publish\SPI\Repository\Validator\ContentValidator;
use eZ\Publish\SPI\Search\Handler as SearchHandler;
use EzPlatform\DraftsTools\API\Repository\Repository as DraftsRepository;
use EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface as DraftPersistenceHandler;

class Repository extends RepositoryCoreInterface implements DraftsRepository
{
    /** @var Mapper\ContentDomainMapper */
    protected $contentDomainMapper;

    /** @var \eZ\Publish\API\Repository\PermissionService */
    private $permissionService;

    /**
     * Instance of DraftTools service.
     *
     * @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface
     */
    private $drfatToolsService;

    /** @var \EzPlatform\DraftsTools\SPI\Persistence\HandlerInterface */
    private $draftsPersistenceHandler;

    public function __construct(
        PersistenceHandler $persistenceHandler,
        SearchHandler $searchHandler,
        BackgroundIndexer $backgroundIndexer,
        RelationProcessor $relationProcessor,
        FieldTypeRegistry $fieldTypeRegistry,
        PasswordHashServiceInterface $passwordHashGenerator,
        ThumbnailStrategy $thumbnailStrategy,
        ProxyDomainMapperFactoryInterface $proxyDomainMapperFactory,
        Mapper\ContentDomainMapper $contentDomainMapper,
        Mapper\ContentTypeDomainMapper $contentTypeDomainMapper,
        Mapper\RoleDomainMapper $roleDomainMapper,
        Mapper\ContentMapper $contentMapper,
        ContentValidator $contentValidator,
        LimitationService $limitationService,
        LanguageResolver $languageResolver,
        PermissionService $permissionService,
        ContentFilteringHandler $contentFilteringHandler,
        LocationFilteringHandler $locationFilteringHandler,
        PasswordValidatorInterface $passwordValidator,
        $serviceSettings,
        DraftPersistenceHandler $draftsPersistenceHandler
    ) {
        parent::__construct(
            $persistenceHandler,
            $searchHandler,
            $backgroundIndexer,
            $relationProcessor,
            $fieldTypeRegistry,
            $passwordHashGenerator,
            $thumbnailStrategy,
            $proxyDomainMapperFactory,
            $contentDomainMapper,
            $contentTypeDomainMapper,
            $roleDomainMapper,
            $contentMapper,
            $contentValidator,
            $limitationService,
            $languageResolver,
            $permissionService,
            $contentFilteringHandler,
            $locationFilteringHandler,
            $passwordValidator,
            [],
            null
        );
        $this->draftsPersistenceHandler = $draftsPersistenceHandler;
        $this->contentDomainMapper = $contentDomainMapper;
        $this->permissionService = $permissionService;
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
            $this->permissionService,
            $this->contentDomainMapper
        );

        return $this->drfatToolsService;
    }
}
