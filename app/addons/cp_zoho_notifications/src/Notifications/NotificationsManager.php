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


namespace Tygh\Addons\CpZohoNotifications\Notifications;

use Tygh\Addons\CpZohoNotifications\ServiceProvider;
use Tygh\Addons\CpZohoNotifications\Service;
use Tygh\Registry;


class NotificationsManager
{
    private $order_id ;
    private $order_status ;

    public function __construct($order_data)
    {
        $this->order_id = $order_data['order_id'];
        $this->order_status = $order_data['status'];    
    }

    public function getNotificationDesc()
    {   
        $desc = "";
        $href_desc = __("cp_zoho_notifications.explain_and_cancel");

        switch ($this->order_status) {
            case ServiceProvider::statusProcessed():
                $desc =  __("cp_zoho_notifications.processed",  
                        array(
                            '[vendor]' => Service::getVendorByOrderId($this->order_id),
                            '[order_id]' => $this->order_id
                        )
                    );
                break;
            case ServiceProvider::statusConfirmed():
                $desc = __("cp_zoho_notifications.confirmed",  
                        array(
                            '[user]' => Service::getUserByOrderId($this->order_id),
                            '[order_id]' => $this->order_id
                        )
                    );
                break;
            case ServiceProvider::statusPaid():
                $desc = __("cp_zoho_notifications.paid",  
                        array(
                            '[vendor]' => Service::getVendorByOrderId($this->order_id),
                            '[order_id]' => $this->order_id
                        )
                    );
                break;
            case ServiceProvider::statusPaidAfterCanCellation():
                $desc = __("cp_zoho_notifications.paid_after_cancellation",  
                        array(
                            '[vendor]' => Service::getVendorByOrderId($this->order_id),
                            '[order_id]' => $this->order_id
                        )
                    );
                break;
            case ServiceProvider::statusCompleted():

                $order_info = fn_get_order_info($this->order_id);

                if (!empty($order_info['shipping'])) {
                    if (Service::isPickupShipping($order_info['shipping'])) {
                        $desc =  __("cp_zoho_notifications.completed_pickup",  
                            array(
                                '[vendor]' => Service::getVendorByOrderId($this->order_id),
                                '[user]' => Service::getUserByOrderId($this->order_id),
                                '[order_id]' => $this->order_id
                            )
                        );
                    }else {
                        $desc =  __("cp_zoho_notifications.completed",  
                            array(
                                '[vendor]' => Service::getVendorByOrderId($this->order_id),
                                '[order_id]' => $this->order_id
                            )
                        );
                    }
                }
                $href_desc = __("cp_zoho_notifications.explain");
                break;
            case ServiceProvider::statusReceived():
                $desc = __("cp_zoho_notifications.received",  
                    array(
                        '[user]' => Service::getUserByOrderId($this->order_id),
                        '[date]' => Service::getReceivedTimeByOrderId($this->order_id),
                        '[order_id]' => $this->order_id
                    )
                );
                $href_desc = __("cp_zoho_notifications.explain");
                break;
        }
        $desc .= "<br>" . $href_desc . " : " . fn_url("orders.details&order_id=" . $this->order_id);
        return $desc;
    }
    public function getNotificationSubj()
    {
        switch ($this->order_status) {
            case ServiceProvider::statusProcessed():
                return __("cp_zoho_notifications.processed_subj");
                break;
            case ServiceProvider::statusConfirmed():
                return __("cp_zoho_notifications.confirmed_subj");
                break;
            case ServiceProvider::statusPaid():
                return __("cp_zoho_notifications.paid_subj");
                break;
            case ServiceProvider::statusPaidAfterCancellation():
               return __("cp_zoho_notifications.paid_after_cancellation_subj");
                break;
            case ServiceProvider::statusCompleted():
                $order_info = fn_get_order_info($this->order_id);

                if (!empty($order_info['shipping'])) {
                    if (Service::isPickupShipping($order_info['shipping'])) {
                        return __("cp_zoho_notifications.completed_pickup_subj");
                    }else {
                        return __("cp_zoho_notifications.completed_subj");
                    }
                }
                
                break;
            case ServiceProvider::statusReceived():
                return __("cp_zoho_notifications.received_subj", [ "[vendor]" => Service::getVendorByOrderId($this->order_id)]);
                break;
        }
    }
}