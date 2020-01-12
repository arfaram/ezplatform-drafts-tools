<?php

namespace EzPlatform\DraftsTools\SPI\Persistence;

interface HandlerInterface
{
    public function getDraftsList($offset, $limit);

    public function countAllDrafts();
}
