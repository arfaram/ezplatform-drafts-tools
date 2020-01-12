<?php

namespace EzPlatform\DraftsTools\API\Repository;

interface DraftsToolsServiceInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function loadAllDrafts($page = 0, $limit = 25);
}
