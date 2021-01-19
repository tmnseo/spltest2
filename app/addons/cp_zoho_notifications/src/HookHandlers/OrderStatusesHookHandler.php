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


namespace Tygh\Addons\CpZohoNotifications\HookHandlers;

use Tygh\Application;
use Tygh\Addons\CpZohoNotifications\ServiceProvider;

class OrderStatusesHookHandler
{
    protected $application;
    
    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * 'change_order_status_post' hook 
     */
    public function postChangeStatus($order_id, $status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data)
    {  
        $order_logger = ServiceProvider::getOrderLogger();

        if (!empty($order_id) && !empty($status_to) && $order_logger->isMonitoredOrderStatus($status_to)) {

            $order_logger->createLogData($order_id, $status_to);
            $order_logger->updateLog();

        }elseif (!empty($order_id)) {
            
            $order_logger->removeLogById($order_id);
        }
    }
}