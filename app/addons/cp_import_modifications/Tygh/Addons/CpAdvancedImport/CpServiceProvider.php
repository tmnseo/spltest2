<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\CpAdvancedImport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\CpAdvancedImport\Readers\CpFactory as CpReadersFactory;
use Tygh\Registry;

class CpServiceProvider implements ServiceProviderInterface
{
    /** @inheritdoc */
    public function register(Container $app)
    {
        $app['addons.advanced_import.readers.factory'] = function (Container $app) {

            $company_id = fn_get_runtime_company_id();

            return new CpReadersFactory($company_id);
        };
    }
}