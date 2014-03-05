<?php

namespace Oro\Bundle\EntityExtendBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MigrationConfigPass implements CompilerPassInterface
{
    const EXTEND_OPTIONS_MANAGER_SERVICE       = 'oro_entity_extend.migration.options_manager';
    const EXTEND_EXTENSION_SERVICE             = 'oro_entity_extend.migration.extension.extend';
    const MIGRATIONS_QUERY_BUILDER_SERVICE     = 'oro_migration.migrations.query_builder';
    const MIGRATIONS_QUERY_BUILDER_CLASS_PARAM = 'oro_migration.migrations.query_builder.class';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::MIGRATIONS_QUERY_BUILDER_SERVICE)
            && $container->hasParameter(self::MIGRATIONS_QUERY_BUILDER_CLASS_PARAM)
        ) {
            $container->setParameter(
                self::MIGRATIONS_QUERY_BUILDER_CLASS_PARAM,
                'Oro\Bundle\EntityExtendBundle\Migration\ExtendMigrationQueryBuilder'
            );
            $serviceDef = $container->getDefinition(self::MIGRATIONS_QUERY_BUILDER_SERVICE);
            $serviceDef->addMethodCall(
                'setExtendOptionsManager',
                [new Reference(self::EXTEND_OPTIONS_MANAGER_SERVICE)]
            );
            $serviceDef->addMethodCall(
                'setExtendExtension',
                [new Reference(self::EXTEND_EXTENSION_SERVICE)]
            );
        }
    }
}
