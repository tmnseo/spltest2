<?php
/*****************************************************************************
 *                                                        © 2013 Cart-Power   *
 *           __   ______           __        ____                             *
 *          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
 *      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
 *     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
 *    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
 *                                                                            *
 *                                                                            *
 * -------------------------------------------------------------------------- *
 * This is commercial software, only users who have purchased a valid license *
 * and  accept to the terms of the License Agreement can install and use this *
 * program.                                                                   *
 * -------------------------------------------------------------------------- *
 * website: https://store.cart-power.com                                      *
 * email:   sales@cart-power.com                                              *
 ******************************************************************************/

namespace Tygh\Addons\CpManageOldProducts;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\CpManageOldProducts\Products\ProductManager;
use Tygh\Addons\CpManageOldProducts\HookHandlers\ImportHookHandler;
use Tygh\Tools\SecurityHelper;
use Tygh\Registry;
use Tygh\Tygh;


/**
 * Class ServiceProvider is intended to register services and components
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    
    public function register(Container $app)
    {   
        $app['addons.cp_manage_old_products.product_manager'] = function (Container $app) {
            return new ProductManager($app);
        };

        $app['addons.cp_manage_old_products.hook_handlers.import'] = function (Container $app) {
            return new ImportHookHandler($app);
        };
    }

    
    /**
    * @return ProductManager
    */
    public static function getProductManager() 
    {
        return Tygh::$app['addons.cp_manage_old_products.product_manager'];
    }

    public static function cronPass()
    {
        return Registry::get('addons.cp_manage_old_products.cron_pass');
    }

    public static function typeOfPartFeatureId()
    {
        return Registry::get('addons.cp_manage_old_products.type_of_part_feature_id');
    }

    public static function productsDaysLifetime()
    {
        return Registry::get('addons.cp_manage_old_products.products_days_lifetime');
    }
    
}

