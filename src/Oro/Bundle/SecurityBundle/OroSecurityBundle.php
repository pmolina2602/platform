<?php

namespace Oro\Bundle\SecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Oro\Bundle\SecurityBundle\Annotation\Loader\AclAnnotationCumulativeResourceLoader;
use Oro\Bundle\SecurityBundle\DependencyInjection\Compiler\AclConfigurationPass;
use Oro\Bundle\SecurityBundle\DependencyInjection\Compiler\AclAnnotationProviderPass;

use Oro\Component\Config\CumulativeResourceManager;

class OroSecurityBundle extends Bundle
{
    /**
     * Constructor
     */
    public function __construct()
    {
        CumulativeResourceManager::getInstance()
            ->registerResource('oro_acl_config', 'Resources/config/acl.yml')
            ->registerResource(
                'oro_acl_annotation',
                new AclAnnotationCumulativeResourceLoader(['Controller'])
            );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AclConfigurationPass());
        $container->addCompilerPass(new AclAnnotationProviderPass());
    }
}
