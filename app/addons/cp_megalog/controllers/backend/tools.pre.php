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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update_status') {
        /*
        Add to megalog changing objects status
        */
        if (!empty($_REQUEST['status']) && $_REQUEST['table'] == 'discussion_posts' && $_REQUEST['id_name'] == 'post_id' && !empty($_REQUEST['id'])) {
            $mode = $_REQUEST['status'] == 'A' ? 'approve' : 'disapprove';
            $types = fn_get_schema('cp_ml', 'types');
            if (!empty($types) && !empty($types['reviews']) && !empty($types['reviews'][$mode])) {
                $req_data = array(
                    'post_id' => $_REQUEST['id']
                );
                $put_data = array(
                    'controller' => 'reviews',
                    'mode' => $mode,
                    'method' => 'post',
                    'timestamp' => time(),
                    'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                    'object_id' => $_REQUEST['id'],
                    'request' => json_encode($req_data)
                );
                fn_cp_megalog_ml_add_log($put_data);
            }
        } elseif (!empty($_REQUEST['status']) && !empty($_REQUEST['table']) && !empty($_REQUEST['id'])) {
            $types = fn_get_schema('cp_ml', 'types');
            if (!empty($types) && !empty($types['tools']) && !empty($types['tools']['update_status'])) {
                $req_data = array(
                    'object_id' => $_REQUEST['id'],
                    'status' => $_REQUEST['status'],
                    'table' => $_REQUEST['table'],
                    'label' => __('status') . ': ' . $_REQUEST['status'] . ', ' . $_REQUEST['id'] . '<br />' . __('table') . ': ' . $_REQUEST['table']
                );
                $put_data = array(
                    'controller' => 'tools',
                    'mode' => 'update_status',
                    'method' => 'post',
                    'timestamp' => time(),
                    'user_id' => !empty(Tygh::$app['session']['auth']['user_id']) ? Tygh::$app['session']['auth']['user_id'] : 0,
                    'object_id' => $_REQUEST['id'],
                    'request' => json_encode($req_data)
                );
                fn_cp_megalog_ml_add_log($put_data);
            }
        }
    }
}