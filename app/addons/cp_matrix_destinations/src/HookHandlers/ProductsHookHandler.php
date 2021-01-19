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


namespace Tygh\Addons\CpMatrixDestinations\HookHandlers;


use Tygh;

use Tygh\Addons\CpMatrixDestinations\ServiceProvider;
use Tygh\Application;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ProductFeatures;
use Tygh\Registry;
use Tygh\Tools\Url;

/**
 * This class describes the hook handlers related to product management
 *
 * @package Tygh\Addons\ProductVariations\HookHandlers
 */
class ProductsHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }



    /**
     * The "get_products" hook handler.
     *
     * Actions performed:
     *  - Implements filtering by parent products
     *  - Implements filtering by product type, based on the following schema: product_variations/product_types
     *  - Implements filtering by linked variations; is used for the following block: "View all variations as list"
     *  - Implements filtering by the presence of features
     *  - Implements filtering by the presence of a product in a variation group
     *  - Implements filtering by the identifier of a variation group
     *  - Determines whether or not to include child variations into selection
     *  - Determines whether or not to group child variations as one product
     *
     * @see fn_get_products
     */
    public function onUpdateProductPost($product_data, $product_id, $lang_code, $create)
    {

        $needed_store_ids = db_get_fields("SELECT warehouse_id FROM ?:warehouses_products_amount WHERE product_id =?i and city_id =?i",$product_id,0);


        if(!empty($needed_store_ids)){
            foreach ($needed_store_ids as $needed_store_id) {

                $matrix_model = ServiceProvider::getMatrix();
                $matrix_model->updateWarehouseAmountsetCityId($needed_store_id);

            }
        }

    }


    public function onGetProductsPost(&$products, $params, $lang_code){
        if(AREA == "C"){
            $matrix_model = ServiceProvider::getMatrix();
            $products = $matrix_model->recalculateDeliveryDate($products,$lang_code);
        }
    }

}
