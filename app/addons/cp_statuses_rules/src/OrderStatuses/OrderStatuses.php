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

namespace Tygh\Addons\CpStatusesRules\OrderStatuses;

use Tygh\Addons\CpStatusesRules\ServiceProvider; 

class OrderStatuses
{

    public static function getCustomerStatusesForCansel()
    {
        return array(
            ServiceProvider::statusPaid(),
            ServiceProvider::statusPlaced()
        );
    }

    public static function getCustomerStatusesForPretension()
    {
        return array(
            ServiceProvider::statusReceived()
        );
    }

    public static function getStatusesForChangeShippingCost()
    {
        return array(
            ServiceProvider::statusPlaced()
        );
    }

    public static function getStatusesBeforeCompleted()
    {
        return array(
            ServiceProvider::statusPaid(),
            ServiceProvider::statusPaidAfterCancellation()
        );
    }

    public static function getStatusesAfterPaid()
    {
        return array(
           ServiceProvider::statusPaid(),
           ServiceProvider::statusPaidAfterCancellation(),
           ServiceProvider::statusCompleted(),
           ServiceProvider::statusShipped(),
           ServiceProvider::statusReceived(),
           ServiceProvider::statusFinished() 
        );
    }

    public static function getStatusesForChangePlannedTimeIssuingOrder()
    {
        return array(
            ServiceProvider::statusPaid(),
            ServiceProvider::statusCompleted()
        );
    }
}