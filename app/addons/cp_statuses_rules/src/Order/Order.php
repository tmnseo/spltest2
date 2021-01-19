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

namespace Tygh\Addons\CpStatusesRules\Order; 

use Tygh\Addons\CpStatusesRules\ServiceProvider;
use Tygh\Addons\CpStatusesRules\Service;

class Order 
{
    private $order_id = 0;
    private $status;
    private $need_statuses_from = [];


    function __construct($order_id)
    {
        $this->order_id = $order_id;

        $this->need_statuses_from = [
            ServiceProvider::statusPlaced(),
            ServiceProvider::statusPaid(),
            ServiceProvider::statusPaidAfterCancellation(),
            ServiceProvider::statusCompleted(),
            ServiceProvider::statusShipped()
        ];

        $this->status = db_get_field("SELECT status FROM ?:orders WHERE order_id = ?i", $this->order_id);
    }

    public function getExcludeLockedStatusesCondition()
    {   
        $condition = '';

        switch ($this->status) {
            case ServiceProvider::statusPlaced():
                $condition = db_quote(" ?:statuses.status = ?s OR ?:statuses.status = ?s OR", ServiceProvider::statusConfirmed(), ServiceProvider::statusCancel());
                break;
            case ServiceProvider::statusPaid():
                $condition = db_quote(" ?:statuses.status = ?s OR", ServiceProvider::statusCompleted());
                break;
            case ServiceProvider::statusPaidAfterCancellation():
                $condition = db_quote(" ?:statuses.status = ?s OR ?:statuses.status = ?s OR", ServiceProvider::statusCompleted(), ServiceProvider::statusRefund());
                break;
            case ServiceProvider::statusCompleted():
                
                if (Service::isPickupShipping($this->order_id)) {

                    $condition = db_quote(" ?:statuses.status = ?s OR ?:statuses.status = ?s OR", ServiceProvider::statusReceived(), ServiceProvider::statusRefund());
                }else {

                    $condition = db_quote("?:statuses.status = ?s OR", ServiceProvider::statusShipped());
                }
                break;
            case ServiceProvider::statusShipped():
                $condition = db_quote(" ?:statuses.status = ?s OR ?:statuses.status = ?s OR ?:statuses.status = ?s OR " , ServiceProvider::statusReceived(), ServiceProvider::statusRefund(), ServiceProvider::statusVendorReturn());
                break;
        }

        return "AND (" . $condition . db_quote(" ?:statuses.status = ?s)", $this->status);
    }

    public function changeStatusAfterPayment()
    {
        switch ($this->status) {
            case ServiceProvider::statusConfirmed():
                fn_change_order_status($this->order_id, ServiceProvider::statusPaid());
                break;
            case ServiceProvider::statusCancel():
                fn_change_order_status($this->order_id, ServiceProvider::statusPaidAfterCancellation());
                break;
            case ServiceProvider::statusWaitingForPayment():
                fn_change_order_status($this->order_id, ServiceProvider::statusPaid());
                break;
        }
    }

    public static function getUserEmail($order_id)
    {
        $email = db_get_field("SELECT email FROM ?:orders WHERE order_id = ?i", $order_id);
        
        return $email;
    }

}