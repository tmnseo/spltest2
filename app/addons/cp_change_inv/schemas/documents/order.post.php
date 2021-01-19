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

$schema['order']['attributes'][] = 'cp_date_with_m';
$schema['order']['attributes'][] = 'cp_str_total';
$schema['order']['attributes'][] = 'cp_total_info';
/*17.02.2020 gmelnikov cart-power modifs */
$schema['order']['attributes'][] = 'cp_path_to_img'; 
/*17.02.2020 gmelnikov cart-power modifs */
/*17.04.2020 gmelnikov cart-power modifs */
$schema['order']['attributes'][] = 'cp_confirm_date';
$schema['order']['attributes'][] = 'cp_warehouse_address';
/*17.04.2020 gmelnikov cart-power modifs */

return $schema;