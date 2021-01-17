<?php

require_once __DIR__ . '/vendor/autoload.php';

use EzSystems\EzPlatformCodeStyle\PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;

class InternalConfigFactory
{
    public const PHP_HEADER = <<<'EOF'
@copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
@license For full copyright and license information view LICENSE file distributed with this source code.
EOF;

    public static function build(): ConfigInterface
    {
        $config = Config::create();

        $specificRules = [
            'header_comment' => [
                'commentType' => 'PHPDoc',
                'header' => static::PHP_HEADER,
                'location' => 'after_open',
                'separate' => 'top',
            ],
        ];
        $config->setRules(array_merge($config->getRules(), $specificRules));

        return $config;
    }
}

return InternalConfigFactory::build()->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src')
        ->files()->name('*.php')
);