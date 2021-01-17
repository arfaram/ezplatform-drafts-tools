<?php

/**
 * @copyright Copyright (C) ramzi arfaoui ramzi_arfa@hotmail.de . All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzPlatform\DraftsToolsBundle;

use EzPlatform\DraftsToolsBundle\DependencyInjection\Security\PolicyProvider\EzPlatformDraftstoolsPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzPlatformDraftsToolsBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ezpublish');
        $kernelExtension->addPolicyProvider(new EzPlatformDraftstoolsPolicyProvider($this->getPath()));
    }
}
