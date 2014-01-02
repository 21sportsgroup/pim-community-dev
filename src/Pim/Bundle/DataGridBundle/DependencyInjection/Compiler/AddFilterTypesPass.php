<?php

namespace Pim\Bundle\DataGridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Add grid filter types
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AddFilterTypesPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    const FILTER_EXTENSION_ID = 'pim_datagrid.extension.filter.orm_filter';

    /**
     * @®ar string
     */
    const TAG_NAME            = 'oro_filter.extension.orm_filter.filter';

    /**
     * @var string
     */
    const PRODUCT_TAG_NAME    = 'oro_filter.extension.orm_product_filter.filter';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        /**
         * Find and add available filters to extension
         */
        $extension = $container->getDefinition(self::FILTER_EXTENSION_ID);
        if ($extension) {
            // add base filters
            $filters = $container->findTaggedServiceIds(self::TAG_NAME);
            foreach ($filters as $serviceId => $tags) {
                $tagAttrs = reset($tags);
                $extension->addMethodCall('addFilter', array($tagAttrs['type'], new Reference($serviceId)));
            }
            // add flexible filters
            $filters = $container->findTaggedServiceIds(self::PRODUCT_TAG_NAME);
            foreach ($filters as $serviceId => $tags) {
                $tagAttrs = reset($tags);
                $extension->addMethodCall('addFilter', array($tagAttrs['type'], new Reference($serviceId)));
            }
        }
    }
}
