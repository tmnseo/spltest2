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
use Tygh\Notifications\Transports\Internal\InternalTransport;
use Tygh\Notifications\Transports\Internal\InternalMessageSchema;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\RecipientSearchMethods;
use Tygh\NotificationsCenter\NotificationsCenter;
use Tygh\Registry;
use Tygh\Addons\CpAdditionalEmailTemplates\Notifications\DataProviders\CpAdditionalEmailTemplatesDataProvider;

defined('BOOTSTRAP') or die('Access denied');

$schema['cp_additional_email_templates.new_connection_request'] = [
    'group'     => 'cp_additional_email_templates', 
    'name'      => [
        'template' => 'cp_additional_email_templates.event.new_connection_request.name',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_support_department',
                'to'              => DataValue::create('request_data.to'),
                'template_code'   => 'cp_additional_email_templates_new_connection_request',
                'legacy_template' => 'addons/cp_additional_email_templates/new_connection_request.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

$schema['cp_additional_email_templates.new_connection_request_sent'] = [
    'group'     => 'cp_additional_email_templates',
    'name'      => [
        'template' => 'cp_additional_email_templates.event.new_connection_request_sent.name',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::VENDOR => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_users_department',
                'to'              => DataValue::create('request_data.email'),
                'template_code'   => 'cp_additional_email_templates_new_connection_request_sent',
                'legacy_template' => 'addons/cp_additional_email_templates/connection_request_sent.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

$schema['cp_additional_email_templates.order_unpaid'] = [
    'group'     => 'cp_additional_email_templates',
    'name'      => [
        'template' => 'cp_additional_email_templates.event.order_unpaid.name',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'C',
                'from'            => 'default_company_users_department',
                'to'              => DataValue::create('request_data.email'),
                'template_code'   => 'cp_additional_email_templates_order_unpaid',
                'legacy_template' => 'addons/cp_additional_email_templates/order_unpaid.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

$schema['cp_additional_email_templates.new_planned_time_issuing_order'] = [
    'group'     => 'cp_additional_email_templates',
    'name'      => [
        'template' => 'cp_additional_email_templates.event.new_planned_time_issuing_order',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::VENDOR => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_users_department',
                'to'              => DataValue::create('request_data.email'),
                'template_code'   => 'cp_additional_email_templates_new_planned_time_issuing_order',
                'legacy_template' => 'addons/cp_additional_email_templates/new_planned_time_issuing_order.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

$schema['cp_additional_email_templates.planned_time_issuing_order_change_for_customer'] = [
    'group'     => 'cp_additional_email_templates',
    'name'      => [
        'template' => 'cp_additional_email_templates.event.planned_time_issuing_order_change_for_customer',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::CUSTOMER => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'C',
                'from'            => 'default_company_users_department',
                'to'              => DataValue::create('request_data.email'),
                'template_code'   => 'planned_time_issuing_order_change_for_customer',
                'legacy_template' => 'addons/cp_additional_email_templates/planned_time_change_for_customer.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

$schema['cp_additional_email_templates.planned_time_issuing_order_change_for_admin'] = [
    'group'     => 'cp_additional_email_templates', 
    'name'      => [
        'template' => 'cp_additional_email_templates.event.planned_time_issuing_order_change_for_admin',
        'params'   => [],
    ],
    'data_provider' => [CpAdditionalEmailTemplatesDataProvider::class, 'factory'],
    'receivers' => [
        UserTypes::ADMIN => [
            MailTransport::getId() => MailMessageSchema::create([
                'area'            => 'A',
                'from'            => 'default_company_support_department',
                'to'              => DataValue::create('request_data.email'),
                'template_code'   => 'planned_time_issuing_order_change_for_admin',
                'legacy_template' => 'addons/cp_additional_email_templates/planned_time_change_for_admin.tpl',
                'company_id'      => 0,
                'language_code'   => Registry::get('settings.Appearance.backend_default_language'),
            ]),
        ],
    ],
];

return $schema;