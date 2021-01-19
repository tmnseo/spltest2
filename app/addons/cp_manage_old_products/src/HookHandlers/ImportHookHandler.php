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

namespace Tygh\Addons\CpManageOldProducts\HookHandlers;

use Tygh;
use Tygh\Application;
use Tygh\Addons\CpManageOldProducts\ServiceProvider;


class ImportHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    public function checkFeatureBeforeUpdate($existent_feature_variants, $feature_id, &$variant_name, $variant_id)
    {
        $type_of_parts_feature_id = ServiceProvider::typeOfPartFeatureId();
        
        if (!empty($feature_id) 
            && !empty($type_of_parts_feature_id) 
            && $type_of_parts_feature_id == $feature_id
            && !empty($variant_name) 
           ){
            
            $feature_variants = $existent_feature_variants[$type_of_parts_feature_id];
            if (!empty($feature_variants) && !isset($feature_variants[$variant_name])) {
                $current_feature_variant = current($feature_variants);
                
                $variant_name = $current_feature_variant['variant'];
            }
            
        }
    }

}
