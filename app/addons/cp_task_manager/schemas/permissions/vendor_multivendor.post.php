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

$auth = $_SESSION['auth'];
if (!isset($auth['user_type'])) {
    $auth['user_type'] = 'V';
}
$vend_allow = fn_cp_aa_check_vendor_permissions_on_tasks();
$schema['controllers']['tasks'] = array (
    'modes' => array (
        'get_server_time' => array (
            'permissions' => true,
        ),
        'manage' => array (
            'permissions' => array ('GET' => !empty($auth['usergroup_ids']) ? 'view_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow), 
                                    'POST' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow)),
        ),
        'add' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow),
        ),
        'update' => array (
            'permissions' => array ('GET' => !empty($auth['usergroup_ids']) ? 'view_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow), 
                                    'POST' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow)),
        ),
        'delete' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow),
        ),
        'm_delete' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow),
        ),
        'clear_logs' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'process' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'view_logs' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'get_file' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : $vend_allow),
        ),
        'set_approve' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'm_approve' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'm_dapprove' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
    ),
    'permissions' => false,
);

$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['cp_tasks'] = 'manage_tasks';

return $schema;