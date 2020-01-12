<?php

namespace EzPlatform\DraftsToolsBundle\Twig;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\LocationService;
use eZ\Publish\Core\Repository\Permission\PermissionResolver;
use Twig_Extension;
use Twig_SimpleFunction;

class DraftParentLocationExtension extends Twig_Extension
{
    /** @var \eZ\Publish\Core\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\Core\Repository\Permission\PermissionResolver */
    private $permissionResolver;

    /**
     * DraftParentLocationExtension constructor.
     * @param \eZ\Publish\Core\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService,
        PermissionResolver $permissionResolver
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * @return array|\Twig\TwigFunction[]
     */
    public function getFunctions()
    {
        return
        [
            new Twig_SimpleFunction(
                'newDraftParentocation',
                [$this, 'getNewDraftParentocations']
            ),
            new Twig_SimpleFunction(
                'parentLocationsOfExistingContent',
                [$this, 'getParentLocationsOfExistingContent']
            ),
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app.draft';
    }

    /**
     * @param $draftContentId
     * @param $draftVersionNumber
     * @return array|\eZ\Publish\API\Repository\Values\Content\Location[]
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\Core\Base\Exceptions\BadStateException
     */
    public function getNewDraftParentocations($draftContentId, $draftVersionNumber)
    {
        $versionInfo = $this->contentService->loadVersionInfoById($draftContentId, $draftVersionNumber);

        $draftParentLoctions = $this->locationService->loadParentLocationsForDraftContent($versionInfo);

        $draftParentLoctions = array_map(
            function ($location) {
                return $this->locationService->loadLocation($location->id);
            }, $draftParentLoctions
        );

        return $draftParentLoctions;
    }

    /**
     * @todo nice to have "loadParentLocationsForDraftContent" also for existing content
     * @param $draftContentId
     * @return array
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function getParentLocationsOfExistingContent($draftContentId)
    {
        try {
            //Not allowed to read other contentTypes
            $contentInfo = $this->contentService->loadContentInfo($draftContentId);
        } catch (\Exception $exception) {
            return;
        }

        $locations = $this->locationService->loadLocations($contentInfo);

        $parentLocations = array_map(function ($location) {
            if ($location->parentLocationId == 1) {
                return;
            }

            return $this->locationService->loadLocation($location->parentLocationId);
        }, $locations
        );

        return $parentLocations;
    }
}
