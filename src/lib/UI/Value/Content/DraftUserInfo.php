<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzPlatform\DraftsTools\UI\Value\Content;

use EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraft;

class DraftUserInfo extends ContentDraft
{
    /** @var mixed */
    private $owner;

    /** @var mixed */
    private $creator;

    /**
     * DraftUserInfo constructor.
     *
     * @param \EzSystems\EzPlatformAdminUi\UI\Value\Content\ContentDraft $contentDraft
     * @param array $properties
     */
    public function __construct(
        ContentDraft $contentDraft,
        array $properties = []
    ) {
        parent::__construct(
            $contentDraft->getVersionInfo(),
            $contentDraft->getVersionId(),
            $contentDraft->getContentType()
        );

        $this->owner = $properties['owner'];
        $this->creator = $properties['creator'];
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
