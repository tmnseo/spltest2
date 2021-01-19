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

$schema['top']['administration']['items']['cp_tasks'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'tasks.manage',
    'position' => 500,
    'subitems' => array(
        'cp_tasks_manage' => array(
            'href' => 'tasks.manage',
            'position' => 100,
        ),
        'cp_add_task' => array(
            'href' => 'tasks.add',
            'position' => 200,
        ),
        'cp_view_logs' => array(
            'href' => 'tasks.view_logs',
            'position' => 300,
        ),
    )
);

return $schema;
