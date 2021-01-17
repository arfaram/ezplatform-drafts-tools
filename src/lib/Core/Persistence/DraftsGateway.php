<?php

declare(strict_types=1);

namespace EzPlatform\DraftsTools\Core\Persistence;

abstract class DraftsGateway
{
    abstract public function getDraftsList($offset, $limit);
}
