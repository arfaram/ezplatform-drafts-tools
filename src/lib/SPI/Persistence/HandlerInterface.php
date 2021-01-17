<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzPlatform\DraftsTools\SPI\Persistence;

interface HandlerInterface
{
    public function getDraftsList($offset, $limit);

    public function countAllDrafts();
}
