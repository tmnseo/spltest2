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

$schema['tasks']['modes']['process']['use_company'] = false;

if (isset($_SESSION['auth'])) {
    $auth = $_SESSION['auth'];
} else {
    $auth = array(
        'user_type' => 'A',
    );
}

if (!isset($auth['user_type'])) {
    $auth['user_type'] = 'A';
}

$schema['tasks'] = array (
    'modes' => array(
        'get_server_time' => array(
            'permissions' => true,
        ),
        'manage' => array (
            'permissions' => array ('GET' => !empty($auth['usergroup_ids']) ? 'view_tasks' : ($auth['user_type'] == 'A' ? true : false), 
                                    'POST' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false)),
        ),
        'add' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'update' => array (
            'permissions' => array ('GET' => !empty($auth['usergroup_ids']) ? 'view_tasks' : ($auth['user_type'] == 'A' ? true : false), 
                                    'POST' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false)),
        ),
        'delete' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
        ),
        'm_delete' => array (
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
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
            'permissions' => !empty($auth['usergroup_ids']) ? 'manage_tasks' : ($auth['user_type'] == 'A' ? true : false),
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

$schema['tools']['modes']['update_status']['param_permissions']['table']['cp_tasks'] = 'manage_tasks';


return $schema;
