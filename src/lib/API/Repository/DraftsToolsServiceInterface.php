<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzPlatform\DraftsTools\API\Repository;

interface DraftsToolsServiceInterface
{
    /**
     * @param int $page
     * @param int $limit
     *
     * @return mixed
     */
    public function loadAllDrafts($page = 0, $limit = 25);
}
