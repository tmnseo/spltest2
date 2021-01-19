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

namespace Tygh\Addons\CpZohoNotifications;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\CpZohoNotifications\HookHandlers\OrderStatusesHookHandler;
use Tygh\Addons\CpZohoNotifications\HookHandlers\CpCrmHookHandler;
use Tygh\Addons\CpZohoNotifications\OrderLogger\OrderLogger;
use Tygh\Tools\SecurityHelper;
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
        $app['addons.cp_zoho_notifications.hook_handlers.order_statuses'] = function (Container $app) {
            return new OrderStatusesHookHandler($app);
        };
        $app['addons.cp_zoho_notifications.hook_handlers.cp_crm'] = function (Container $app) {
            return new CpCrmHookHandler($app);
        };
        $app['addons.cp_zoho_notifications.order_logger'] = function (Container $app) {
            return new OrderLogger();
        };
    }
    /**
     * @return char
     */
    public static function statusProcessed()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_processed');
    }
    /**
     * @return char
     */
    public static function statusConfirmed()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_confirmed');
    }
    /**
     * @return char
     */
    public static function statusPaid()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_paid');
    }
    /**
     * @return char
     */
    public static function statusPaidAfterCancellation()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_paid_after_cancellation');
    }
    /**
     * @return char
     */
    public static function statusCompleted()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_completed');
    }

    public static function statusReceived()
    {
        return Registry::get('addons.cp_zoho_notifications.order_status_received');
    }
    /**
    * @return OrderLogger
    */
    public static function getOrderLogger() 
    {
        return Tygh::$app['addons.cp_zoho_notifications.order_logger'];
    }
}

