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

use Tygh\Enum\UserTypes;
use Tygh\Notifications\DataValue;
use Tygh\Notifications\Transports\Mail\MailTransport;
use Tygh\Notifications\Transports\Mail\MailMessageSchema;
use Tygh\Registry;
use Tygh\Addons\CpWarehousesPremoderation\Notifications\DataProviders\PremoderationRequestDataProvider;

defined('BOOTSTRAP') or die('Access denied');

$schema['cp_warehouses_premoderation.premoderation_request_created'] = [
    'group'     => 'cp_warehouses_premoderation',
    'name'      => [
        'template' => 'cp_warehouses_premoderation.event.premoderation_request_created.name',
        'params'   => [],
    ],
    'data_provider' => [PremoderationRequestDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_support_department',
                'to'              => DataValue::create('premoderation_request_data.to'),
                'template_code'   => 'cp_warehouses_premoderation_request',
                'legacy_template' => 'addons/cp_warehouses_premoderation/premoderation_request.tpl',
                'company_id'      => DataValue::create('premoderation_request_data.company_id'),
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];
$schema['cp_warehouses_premoderation.request_approved'] = [
    'group'     => 'cp_warehouses_premoderation',
    'name'      => [
        'template' => 'cp_warehouses_premoderation.event.request_approved.name',
        'params'   => [],
    ],
    'data_provider' => [PremoderationRequestDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::VENDOR => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_support_department',
                'to'              => DataValue::create('premoderation_request_data.to'),
                'template_code'   => 'cp_warehouses_premoderation_request_result',
                'legacy_template' => 'addons/cp_warehouses_premoderation/request_result.tpl',
                'company_id'      => DataValue::create('premoderation_request_data.company_id'),
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];
$schema['cp_warehouses_premoderation.request_disapproved'] = [
    'group'     => 'cp_warehouses_premoderation',
    'name'      => [
        'template' => 'cp_warehouses_premoderation.event.request_disapproved.name',
        'params'   => [],
    ],
    'data_provider' => [PremoderationRequestDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::VENDOR => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_support_department',
                'to'              => DataValue::create('premoderation_request_data.to'),
                'template_code'   => 'cp_warehouses_premoderation_request_result',
                'legacy_template' => 'addons/cp_warehouses_premoderation/premoderation_request.tpl',
                'company_id'      => DataValue::create('premoderation_request_data.company_id'),
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

return $schema;