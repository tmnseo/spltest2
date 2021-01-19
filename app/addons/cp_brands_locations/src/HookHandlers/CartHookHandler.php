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

class CartHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * 'exclude_products_from_calculation' hook 
     */
    public function excludeProductsFromCalculation(&$cart, $auth, $original_subtotal, $subtotal)
    {   
        if (AREA == 'C') {

            $notice = false;
            $products_text = "";

            $current_city_id = !empty(Tygh::$app['session']['cp_user_has_defined_city']) ? Tygh::$app['session']['cp_user_has_defined_city'] : 0;

            if (!empty($current_city_id)) {

                $destination_id = LocationManager::getCustomerCurrentDestinationId($current_city_id);

                if (!empty($destination_id)) {

                    if (!empty($cart['products'])) {
                        foreach ($cart['products'] as $k => $product_data) {

                            $brand_feature_id = ServiceProvider::brandFeatureId();

                            $current_company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $product_data['product_id']);

                            $current_brand_variant = db_get_field("SELECT variant_id FROM ?:product_features_values WHERE feature_id = ?i AND product_id = ?i AND lang_code = ?s", $brand_feature_id, $product_data['product_id'], DESCR_SL);

                            if (!empty($current_company_id) && !empty($current_brand_variant)) {

                                $vendor_brand_destinations = LocationManager::getVendorDestinationsForBrand($current_company_id, $current_brand_variant);

                                // 1 - main destination 
                                if (empty($vendor_brand_destinations) || in_array($destination_id, $vendor_brand_destinations)) {
                                    return;
                                }else {
                                    fn_delete_cart_product($cart, $k);
                                    $products_text .= "- " . $product_data['product'] . "<br>";
                                    $notice = true;
                                }
                            }
                        }
                    }
                }
            }

            if ($notice) {
                fn_set_notification('N', __('notice'), __('cp_brands_locations.reorder_block_product_notice', ["[products_text]" => $products_text]));
            }           
        }
    }
}