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


namespace Tygh\Addons\CpBrandsLocations\HookHandlers;

use Tygh\Application;
use Tygh\Registry;
use Tygh\Addons\CpBrandsLocations\Location\LocationManager;
use Tygh\Addons\CpBrandsLocations\ServiceProvider;
use Tygh;

class ProductsHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * 'get_products' hook 
     */
    public function onGetProducts($params, &$fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having)
    {   

        if (AREA == 'C' || (!empty($params['area']) && $params['area'] == 'C')) {
            $current_city_id = !empty(Tygh::$app['session']['cp_user_has_defined_city']) ? Tygh::$app['session']['cp_user_has_defined_city'] : 0;

            if (!empty($current_city_id)) {

                $destination_id = LocationManager::getCustomerCurrentDestinationId($current_city_id);

                if (!empty($destination_id)) {

                    $brand_feature_id = ServiceProvider::brandFeatureId();

                    $join .= db_quote(" LEFT JOIN ?:product_features_values as cppfv ON products.product_id = cppfv.product_id AND cppfv.feature_id = ?i AND cppfv.lang_code = ?s", $brand_feature_id, DESCR_SL);

                    $join .= db_quote(" LEFT JOIN ?:cp_brands_locations as cpbl ON cppfv.variant_id = cpbl.brand_variant_id AND cpbl.company_id = products.company_id");
                    
                    $condition .= db_quote(" AND (cpbl.destination_id = ?i  OR cpbl.destination_id IS NULL)", $destination_id);
                }
            }
        }   
    }
    /**
     * 'get_product_data' hook 
     */
    public function onGetProductData($product_id, $field_list, &$join, $auth, $lang_code, &$condition)
    {   

        if (AREA == 'C') {
            $current_city_id = !empty(Tygh::$app['session']['cp_user_has_defined_city']) ? Tygh::$app['session']['cp_user_has_defined_city'] : 0;

            if (!empty($current_city_id)) {

                $destination_id = LocationManager::getCustomerCurrentDestinationId($current_city_id);

                if (!empty($destination_id)) {

                    $brand_feature_id = ServiceProvider::brandFeatureId();

                    $join .= db_quote(" LEFT JOIN ?:product_features_values as cppfv ON ?:products.product_id = cppfv.product_id AND cppfv.feature_id = ?i AND cppfv.lang_code = ?s", $brand_feature_id, DESCR_SL);

                    $join .= db_quote(" LEFT JOIN ?:cp_brands_locations as cpbl ON cppfv.variant_id = cpbl.brand_variant_id AND cpbl.company_id = ?:products.company_id");
                    
                    $condition .= db_quote(" AND (cpbl.destination_id = ?i OR cpbl.destination_id IS NULL)", $destination_id);
                    
                }
            }
        }   
    }
    
}