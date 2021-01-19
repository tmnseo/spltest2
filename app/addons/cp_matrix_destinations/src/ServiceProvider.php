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

namespace Tygh\Addons\CpMatrixDestinations;

use Tygh\Addons\CpMatrixDestinations\GeoIp2\Database\CpGeoReader;
use Tygh\Addons\CpMatrixDestinations\GeoIp2\Database;
use Tygh\Addons\CpMatrixDestinations\GeoIp2\CpGeoFactory;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\CpMatrixDestinations\HookHandlers\ProductsHookHandler;
use Tygh\Addons\CpMatrixDestinations\HookHandlers\StoreHookHandler;
use Tygh\Addons\CpMatrixDestinations\HookHandlers\ToolsHookHandler;
use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Matrix\Matrix;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh\Addons\CpMatrixDestinations\Precity\Precity;

//use Tygh\Addons\CpMatrixDestinations\GeoIp2\CpGeoFactory;
use Tygh\Tools\SecurityHelper;
use Tygh\Registry;
use Tygh\Tygh;


/**
 * Class ServiceProvider is intended to register services and components of the "Product variations" add-on to the application
 * container.
 *
 * @package Tygh\Addons\ProductVariations
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
       // $app['addons.product_variations.product.group.repository'] = function (Container $app) {
            //return new ProductGroupRepository(self::getQueryFactory(), DEFAULT_LANGUAGE);
       // };


        $app['addons.cp_matrix_destinations.hook_handlers.tools'] = function (Container $app) {
            return new ToolsHookHandler($app);
        };

       $app['addons.cp_matrix_destinations.precity'] = function (Container $app) {
           return new Precity();
       };


        $app['addons.cp_matrix_destinations.service'] = function (Container $app) {
            return new Service(
                
            );
        };
        
        $app['addons.cp_matrix_destinations.hook_handlers.products'] = function (Container $app) {
            return new ProductsHookHandler($app);
        };


        $app['addons.cp_matrix_destinations.city.city'] = function (Container $app) {
            return new City();
        };


        $app['addons.cp_matrix_destinations.matrix'] = function (Container $app) {
            return new Matrix();
        };

        $app['addons.cp_matrix_destinations.geo'] = function (Container $app) {
          
            require_once(Registry::get('config.dir.addons'). 'cp_matrix_destinations/src/GeoIp2/vendor/autoload.php');

            $reader = new CpGeoReader(Registry::get('config.dir.addons'). 'cp_matrix_destinations/src/GeoIp2/GeoLite2-City.mmdb');
            return $reader;
        };






        $app['addons.cp_matrix_destinations.hook_handlers.store'] = function (Container $app) {
            return new StoreHookHandler($app);
        };



    }

    /**
     * @return Service
     */
    public static function getService()
    {
        return Tygh::$app['addons.cp_matrix_destinations.service'];
    }


    /**
     * @return Service
     */
    public static function getCity()
    {
        return Tygh::$app['addons.cp_matrix_destinations.city.city'];
    }
    
    public static function getPreCity(){
        return Tygh::$app['addons.cp_matrix_destinations.precity'];

    }


    /**
     * @return Service
     */
    public static function getMatrix()
    {
        return Tygh::$app['addons.cp_matrix_destinations.matrix'];
    }

    
    public static function getGeo(){
        return Tygh::$app['addons.cp_matrix_destinations.geo'];

    }


    /**
     * @param array $product_data
     *
     * @internal
     */
    public static function notifyIfProductIsOldProductVariation(array $product_data)
    {
        $product_type = isset($product_data['product_type']) ? $product_data['product_type'] : null;

        if (empty($product_data['__variation_options']) || !in_array($product_type, ['C', 'V'])) {
            return;
        }

        if (!fn_check_permissions('product_variations_converter', 'process', 'admin', 'POST')) {
            return;
        }

        fn_set_notification(
            'W',
            __('warning'),
            __('product_variations.notice.is_old_product_variation', [
                '[convert_url]' => fn_url('product_variations_converter.process?by_combinations=0&by_variations=1&switch_company_id=0'),
            ]),
            'S',
            'is_old_product_variation'
        );
    }
}

