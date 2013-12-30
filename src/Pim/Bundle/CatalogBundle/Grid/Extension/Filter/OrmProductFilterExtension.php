<?php

namespace Pim\Bundle\CatalogBundle\Grid\Extension\Filter;

use Oro\Bundle\FilterBundle\Grid\Extension\OrmFilterExtension;
use Oro\Bundle\FilterBundle\Grid\Extension\Configuration;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\Builder;
use Pim\Bundle\CatalogBundle\Datasource\Orm\OrmProductDatasource;

/**
 * Orm product filter extension
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrmProductFilterExtension extends OrmFilterExtension
{
    /**
     * {@inheritDoc}
     */
    public function isApplicable(DatagridConfiguration $config)
    {
        $filters = $config->offsetGetByPath(Configuration::COLUMNS_PATH, []);

        if (!$filters) {
            return false;
        }

        return $config->offsetGetByPath(Builder::DATASOURCE_TYPE_PATH) == OrmProductDatasource::TYPE;
    }
}