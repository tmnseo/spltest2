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
use Tygh\Registry;
use Tygh\Addons\CpStatusesRules\ServiceProvider;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'calculate_shipping_cost') {

        if (empty($_REQUEST['edost_data']) || empty($_REQUEST['edost_data']['order_id'])) {
            return ;
        }

        $edost_data = $_REQUEST['edost_data'];

        
        list($new_shipping_cost, $old_shipping_cost) = fn_cp_get_shipping_cost($edost_data);

        if (empty($new_shipping_cost)) {
                
            fn_cp_edost_improvement_add_log($edost_data['order_id'], __('cp_edost_improvement.shipping_cost_not_calculated'));
                
            fn_set_notification('W', __('warning'), __('cp_edost_improvement.shipping_cost_not_calculated'));

            Tygh::$app['view']->assign('edost_data', $_REQUEST['edost_data']);

            $msg = Tygh::$app['view']->fetch('addons/cp_edost_improvement/components/calculation_error.tpl');

            fn_set_notification('I',
                __('cp_edost_improvement.calculation_error'),
                $msg,
                'I'
            );

            return;
        }

        if ($old_shipping_cost == $new_shipping_cost) {

            fn_cp_edost_improvement_save_edost_data($edost_data);

            fn_cp_edost_improvement_add_log($edost_data['order_id'], __('cp_edost_improvement.shipping_cost_not_change'));

            fn_set_notification('W', __('warning'), __('cp_edost_improvement.shipping_cost_not_change'));

            return;
        }

        $order_id = fn_cp_change_shipping_cost($edost_data['order_id'], $new_shipping_cost);
        
        
        if ($order_id) {

            fn_cp_edost_improvement_save_edost_data($edost_data);

            $descr = __('cp_edost_improvement.shipping_cost_change', ["[from]" => $old_shipping_cost, "[to]" => $new_shipping_cost]);

            fn_set_notification('N', __('notice'), $descr);

            fn_cp_edost_improvement_add_log($edost_data['order_id'], $descr);

            return array(CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id='.$order_id);
        }

    }elseif ($mode == 'save_after_error') {

        if (empty($_REQUEST['edost_data']) || empty($_REQUEST['edost_data']['order_id'])) {
            return ;
        }

        $edost_data = $_REQUEST['edost_data'];

        fn_cp_edost_improvement_save_edost_data($edost_data);

        return array(CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id=' . $edost_data['order_id']);
    }

    return ;
}
if ($mode == 'calculation_popup') {

    if (!empty($_REQUEST['order_id'])) {
        Tygh::$app['view']->assign('order_id', $_REQUEST['order_id']);
    }
    if (!empty($_REQUEST['is_only_save'])) {
        Tygh::$app['view']->assign('is_only_save', $_REQUEST['is_only_save']);
    }

    Tygh::$app['view']->display('addons/cp_edost_improvement/views/edost/calculation_popup.tpl');

}elseif ($mode == 'size_checker_popup') {

    if (Registry::get('runtime.company_id')
        && !empty($_REQUEST['id']) 
        && !empty($_REQUEST['status']) 
        && $_REQUEST['status'] == ServiceProvider::statusCompleted()) {

        Tygh::$app['view']->assign([
            'status'        => $_REQUEST['status'],
            'order_id'      => $_REQUEST['id'],
            'return_url'    => !empty($_REQUEST['return_url']) ? $_REQUEST['return_url'] : '',
            'result_ids'    => !empty($_REQUEST['result_ids']) ? $_REQUEST['result_ids'] . ",cp_statuses_content" : '',
        ]);

        $edost_data = fn_cp_edost_improvement_get_edost_data_for_order($_REQUEST['id']);

        if (!empty($edost_data)) {
            Tygh::$app['view']->assign('edost_data', $edost_data);
            
        }
        Tygh::$app['view']->display('addons/cp_edost_improvement/components/size_before_completed.tpl');
    }
}

