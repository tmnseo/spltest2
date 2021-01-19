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

namespace Tygh\Addons\CpBrandsLocations;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Tools\SecurityHelper;
use Tygh\Addons\CpBrandsLocations\HookHandlers\ProductsHookHandler;
use Tygh\Addons\CpBrandsLocations\HookHandlers\OrdersHookHandler;
use Tygh\Addons\CpBrandsLocations\HookHandlers\CartHookHandler;
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
        $app['addons.cp_brands_locations.hook_handlers.products'] = function (Container $app) {
            return new ProductsHookHandler($app);
        };
        $app['addons.cp_brands_locations.hook_handlers.orders'] = function (Container $app) {
            return new OrdersHookHandler($app);
        };
        $app['addons.cp_brands_locations.hook_handlers.cart'] = function (Container $app) {
            return new CartHookHandler($app);
        };
       
    }

    public static function brandFeatureId()
    {
        return Registry::get('addons.cp_vendor_panel.feature_brand');
    }
}

