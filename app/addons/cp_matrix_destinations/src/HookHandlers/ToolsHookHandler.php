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
use Tygh\Application;
use Tygh\Addons\CpMatrixDestinations\Geo\Geo;


/**
 * This class describes the hook handlers related to product management
 *
 * @package Tygh\Addons\ProductVariations\HookHandlers
 */
class ToolsHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    public function onGetTools($params, $old_status, $status_data, $condition)
    {

        //fn_print_r($params);
       // fn_print_die($status_data);


       // Geo::convertPrecityToCity($params);

    }

}
