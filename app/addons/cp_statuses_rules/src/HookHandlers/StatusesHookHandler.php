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


namespace Tygh\Addons\CpStatusesRules\HookHandlers;

use Tygh\Registry;
use Tygh\Application;
use Tygh\Addons\CpStatusesRules\Order\Order;
use Tygh\Addons\CpStatusesRules\ServiceProvider;

class StatusesHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * 'get_statuses' hook 
     */
    public function onGetStatuses($join, &$condition, $type, $status_to_select, $additional_statuses, $exclude_parent, $lang_code, $company_id, $order)
    {  
        $company_id = Registry::get('runtime.company_id');
        $order_id = !empty($_REQUEST['order_id']) ? $_REQUEST['order_id'] : 0;

        if (empty($company_id) || empty($order_id) || $type != STATUSES_ORDER) {
            return;
        }

        $cp_order = new Order($order_id);
        
        $condition .= $cp_order->getExcludeLockedStatusesCondition(); 
    }
    /**
     * 'change_order_status_post' hook 
     */
    public function postChangeStatus($order_id, $status_to, $status_from, $force_notification, $place_order, $order_info, $edp_data)
    {   

        $order_logger = ServiceProvider::getStatusesRulesOrderLogger();
        
        if (!empty($order_id) && !empty($status_to) && $order_logger->isMonitoredOrderStatus($status_to)) {

            $order_logger->createLogData($order_id, $status_to, 'M');
            $order_logger->updateLog();

        }elseif (!empty($order_id)) {
            
            $order_logger->removeLogById($order_id, 'M');
        }
    }
}