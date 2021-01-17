<?php

namespace EzPlatform\DraftsToolsBundle\Twig;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

class DraftParentLocationExtension extends AbstractExtension
{
    /** @var \eZ\Publish\Core\Repository\LocationService */
    private $locationService;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /**
     * DraftParentLocationExtension constructor.
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(
        LocationService $locationService,
        ContentService $contentService
    ) {
        $this->locationService = $locationService;
        $this->contentService = $contentService;
    }

    /**
     * @return array|\Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return
        [
            new TwigFunction(
                'newDraftParentocation',
                [$this, 'getNewDraftParentocations']
            ),
            new TwigFunction(
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
    public function getName(): string
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
            },
            $draftParentLoctions
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
        $contentInfo = $this->contentService->loadContentInfo($draftContentId);

        $locations = $this->locationService->loadLocations($contentInfo);

        $parentLocations = array_map(
            function ($location) {
            if ($location->parentLocationId !== 1) {
                return $this->locationService->loadLocation($location->parentLocationId);
            }

            return [];
        },
            $locations
        );

        return $parentLocations;
    }

    /**
     * @param Environment $environment
     */
    public function initRuntime(Environment $environment)
    {
        // TODO: Implement initRuntime() method.
    }

    /**
     * @return array|void
     */
    public function getGlobals(): array
    {
        // TODO: Implement getGlobals() method.
    }
}
