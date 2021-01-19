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
use Tygh\Registry;
use Tygh\Addons\CpZohoNotifications\Service;
use Zoho;

require_once Registry::get('config.dir.addons'). 'nm_title/Zoho/Zoho.php';

class CpCrmHookHandler
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    /**
     * 'cp_crm_process_payments' hook 
     */
    public function postProcessPayments($order_id, $data, $status_answer)
    {   

        if (!empty($order_id) && $status_answer == '500') {
            
            $zoho = new Zoho();
            
            $contact = $zoho->createContact("ADMIN", Registry::get('settings.Company.company_site_administrator'));
            
            if (!empty($contact->id)) {
                $payment_order_number = !empty($data['payment_order_number']) ? $data['payment_order_number'] : 0;
                $desc = __("cp_zoho_notifications.crm",  
                            array(
                                '[user]' => Service::getUserByOrderId($order_id),
                                '[payment_number]' => $payment_order_number
                            )
                        );
                $zoho->newNotification($contact->id, $desc, __("cp_zoho_notifications.crm_subj"));
            }
        }
    }
}