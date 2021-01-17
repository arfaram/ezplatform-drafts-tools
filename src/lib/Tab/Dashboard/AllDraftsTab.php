<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\Tab\Dashboard;

use EzPlatform\DraftsTools\Pagination\Pagerfanta\ContentAllDraftsAdapter;
use eZ\Publish\API\Repository\PermissionResolver;
use EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface;
use EzPlatform\DraftsTools\UI\Dataset\DatasetFactory;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use EzSystems\EzPlatformAdminUi\Tab\ConditionalTabInterface;
use EzSystems\EzPlatformAdminUi\Tab\OrderedTabInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper;

class AllDraftsTab extends AbstractTab implements OrderedTabInterface, ConditionalTabInterface
{
    private const PAGINATION_PARAM_NAME = 'page';

    /** @var \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface */
    private $draftsToolsService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \EzPlatform\DraftsTools\UI\Dataset\DatasetFactory */
    private $datasetFactory;

    /** @var int set from parameters.yaml */
    private $paginationLimit;

    private $defaultPaginationLimit = 25;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /**
     * AllDraftsTab constructor.
     *
     * @param \Twig\Environment $twig
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \EzSystems\EzPlatformAdminUi\Tab\Dashboard\PagerContentToDataMapper $pagerContentToDataMapper
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \eZ\Publish\API\Repository\PermissionResolver $permissionResolver
     * @param \EzPlatform\DraftsTools\UI\Dataset\DatasetFactory $datasetFactory
     * @param \EzPlatform\DraftsTools\API\Repository\DraftsToolsServiceInterface $draftsToolsService
     * @param int $paginationLimit
     */
    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        PagerContentToDataMapper $pagerContentToDataMapper,
        RequestStack $requestStack,
        PermissionResolver $permissionResolver,
        DatasetFactory $datasetFactory,
        DraftsToolsServiceInterface $draftsToolsService,
        ?int $paginationLimit
    ) {
        parent::__construct($twig, $translator);

        $this->pagerContentToDataMapper = $pagerContentToDataMapper;
        $this->requestStack = $requestStack;
        $this->draftsToolsService = $draftsToolsService;
        $this->permissionResolver = $permissionResolver;
        $this->datasetFactory = $datasetFactory;
        $this->paginationLimit = $paginationLimit;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'all_drafts_tab';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->translator->trans('tab.name.all.drafts', [], 'dashboard');
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1000;
    }

    /**
     * Get information about tab presence.
     *
     * @param array $parameters
     *
     * @return bool
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function evaluate(array $parameters): bool
    {
        // hide tab if user has absolutely no access to content/versionread
        return false !== $this->permissionResolver->hasAccess('content', 'versionread') && $this->permissionResolver->hasAccess('ezplatformdraftstools', 'dashboard_tab_all');
    }

    /**
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderView(array $parameters): string
    {
        $currentPage = $this->requestStack->getCurrentRequest()->query->getInt(
            self::PAGINATION_PARAM_NAME,
            1
        );

        $pagination = new Pagerfanta(
            new ContentAllDraftsAdapter($this->draftsToolsService, $this->datasetFactory)
        );
        $pagination->setMaxPerPage($this->paginationLimit ?? $this->defaultPaginationLimit);
        $pagination->setCurrentPage(min(max($currentPage, 1), $pagination->getNbPages()));

        return $this->twig->render('@ezdesign/dashboard/tab/all_draft_list.html.twig', [
            'data' => $pagination->getCurrentPageResults(),
            'pager' => $pagination,
            'pager_options' => [
                'pageParameter' => '[' . self::PAGINATION_PARAM_NAME . ']',
            ],
        ]);
    }
}
