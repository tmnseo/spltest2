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

namespace Tygh\Addons\CpStatusesRules;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\CpStatusesRules\HookHandlers\StatusesHookHandler;
use Tygh\Addons\CpStatusesRules\Order\StatusesRulesOrderLogger;
use Tygh\Addons\CpStatusesRules\HookHandlers\CpCrmHookHandler;
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
        $app['addons.cp_statuses_rules.hook_handlers.statuses'] = function (Container $app) {
            return new StatusesHookHandler($app);
        };
        $app['addons.cp_statuses_rules.hook_handlers.cp_crm'] = function (Container $app) {
            return new CpCrmHookHandler($app);
        };
        $app['addons.cp_statuses_rules.order_logger'] = function (Container $app) {
            return new StatusesRulesOrderLogger();
        };
    }
    /**
     * @return char
     */
    public static function statusPlaced()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_placed');
    }
    /**
     * @return char
     */
    public static function statusCancel()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_cancel');
    }
    /**
     * @return char
     */
    public static function statusPaid()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_paid');
    }
    /**
     * @return char
     */
    public static function statusPaidAfterCancellation()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_paid_after_cancellation');
    }
    /**
     * @return char
     */
    public static function statusConfirmed()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_confirmed');
    }
    /**
     * @return char
     */
    public static function statusRefund()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_refund');
    }
    /**
     * @return char
     */
    public static function statusCancelWithRefund()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_cancel_with_refund');
    }
    /**
     * @return char
     */
    public static function statusCompleted()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_completed');
    }
    /**
     * @return char
     */
    public static function statusReceived()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_received');
    }
    public static function statusShipped()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_shipped');
    }

    public static function statusWaitingForPayment()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_waiting_for_payment');
    }
    public static function statusVendorReturn()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_vendor_return');
    }
    public static function statusFinished()
    {
        return Registry::get('addons.cp_statuses_rules.order_status_finished');
    }
    public static function cronPass()
    {
        return Registry::get('addons.cp_statuses_rules.cron_pass');
    }
    public static function enableTestMenu()
    {
        return Registry::get('addons.cp_statuses_rules.enable_test_menu');
    }
    /**
    * @return OrderLogger
    */
    public static function getStatusesRulesOrderLogger() 
    {
        return Tygh::$app['addons.cp_statuses_rules.order_logger'];
    }
}

