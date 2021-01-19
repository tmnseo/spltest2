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

$schema['controllers']['cp_warehouses_premoderation']['modes']['store_locator_update']['permissions'] = array('GET' => true, 'POST' => true);
$schema['controllers']['cp_warehouses_premoderation']['modes']['update']['permissions'] = array('GET' => true, 'POST' => true);
$schema['controllers']['cp_warehouses_premoderation']['modes']['manage']['permissions'] = array('GET' => true, 'POST' => true);
$schema['controllers']['cp_warehouses_premoderation']['modes']['disapprove_manage']['permissions'] = array('GET' => true, 'POST' => true);
$schema['controllers']['cp_warehouses_premoderation']['modes']['delete']['permissions'] = array('GET' => true, 'POST' => true);
$schema['controllers']['cp_warehouses_premoderation']['modes']['m_delete']['permissions'] = array('GET' => true, 'POST' => true);

return $schema;