<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

// We use EzSystems\EzPlatformAdminUi\UI\Value\ValueFactory Instead s. AllDraftsTab

namespace EzPlatform\DraftsTools\UI\Services;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository as RepositoryInterface;
use eZ\Publish\API\Repository\UserService;
use EzPlatform\DraftsTools\UI\Value\Content\DraftUserInfo;
use Psr\Log\LoggerInterface;

class DraftUserInformationService
{
    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /**
     * AllDraftsValueFactory constructor.
     *
     * @param \eZ\Publish\API\Repository\UserService $userService
     * @param \eZ\Publish\API\Repository\Repository $repository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        UserService $userService,
        RepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        $this->userService = $userService;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    /**
     * @param $contentDraft
     *
     * @return \EzPlatform\DraftsTools\UI\Value\Content\DraftUserInfo
     *
     * @throws \Exception
     */
    public function getDraftUserInformation($contentDraft): DraftUserInfo
    {
        $ownerId = $contentDraft->getVersionInfo()->getContentInfo()->ownerId;
        $creatorId = $contentDraft->getVersionInfo()->creatorId;

        if ($ownerId == $creatorId) {
            $UserContentDraft = $this->repository->sudo(
                function () use ($contentDraft, $ownerId) {
                    $owner = '';
                    try {
                        $owner = $this->userService->loadUser($ownerId)->getVersionInfo();
                    } catch (NotFoundException $exception) {
                        //user has been removed
                        $this->logger->warning(sprintf(
                            'Unable to fetch owner content for contentId %s, (original exception: %s)',
                            $ownerId,
                            $exception->getMessage()
                        ));
                    }

                    return new DraftUserInfo(
                        $contentDraft,
                        [
                            'owner' => $owner,
                            'creator' => $owner,
                        ]
                    );
                }
            );
        } else {
            $UserContentDraft = $this->repository->sudo(
                function () use ($contentDraft, $creatorId, $ownerId) {
                    $owner = '';
                    $creator = '';

                    try {
                        $owner = $this->userService->loadUser($ownerId)->getVersionInfo();
                    } catch (NotFoundException $exception) {
                        //user has been deleted
                        $this->logger->warning(sprintf(
                            'Unable to fetch owner content for contentId %s, (original exception: %s)',
                            $ownerId,
                            $exception->getMessage()
                        ));
                    }

                    try {
                        $creator = $this->userService->loadUser($creatorId)->getVersionInfo();
                    } catch (NotFoundException $exception) {
                        //user has been deleted
                        $this->logger->warning(sprintf(
                            'Unable to fetch creator content for contentId %s, (original exception: %s)',
                            $creatorId,
                            $exception->getMessage()
                        ));
                    }

                    return new DraftUserInfo(
                        $contentDraft,
                        [
                            'owner' => $owner,
                            'creator' => $creator,
                        ]
                    );
                }
            );
        }

        return $UserContentDraft;
    }
}
