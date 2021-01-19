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

$menu = array(
    'position' => 600,
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'subitems' => array(
        'cp_matrix_destinations_rebuild' => array(
            'href' => 'cp_matrix_destinations.rebuild',
            'position' => 110
        ),


        
        'cp_matrix_destinations_recalculate' => array(
            'href' => 'cp_matrix_destinations.recalculate',
            'position' => 110
        ),
        
        'cp_matrix_destinations_manage' => array(
            'href' => 'cp_matrix_destinations.manage',
            'position' => 120
        ),
        'cp_city_manage' => array(
            'href' => 'cp_city.manage',
            'position' => 130
        ),


        'cp_pre_city_manage' => array(
            'href' => 'cp_pre_city.manage',
            'position' => 130
        ),

        'cp_matrix_settings' => array(
            'href' => 'cp_matrix_settings.manage',
            'position' => 130
        ),


        'cp_matrix_log' => array(
            'href' => 'cp_matrix_log.manage',
            'position' => 130
        ),





    ),
    'href' => 'cp_matrix_destinations.manage',
);

if (isset($schema['central']['cart_power_addons'])) {
    $schema['central']['cart_power_addons']['items']['cp_matrix_destinations'] = $menu;
} else {
    $schema['central']['website']['items']['cp_matrix_destinations'] = $menu;
}

return $schema;
