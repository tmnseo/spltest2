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

/* HOOKS */
function fn_cp_additional_email_templates_send_form($page_data, $form_values, $result, $from, $sender, $attachments, $is_html, $subject)
{   
    $vendor_reg_page_id = Registry::get('addons.cp_spl_theme.id_page_profiles_add');
    $event_dispatcher = Tygh::$app['event.dispatcher'];
    $development_specialist_email = Registry::get('addons.cp_additional_email_templates.development_specialist_email');

    if (Registry::get('addons.cp_restrictions_for_vendors.status') == 'A') {
        $knowledge_base = Registry::get('addons.cp_restrictions_for_vendors.support_href');
    }else {
        $knowledge_base = "https://support.service.parts/portal/ru/kb/vendors";
    }

    if (!empty($page_data['page_id']) && $page_data['page_id'] == $vendor_reg_page_id) {
        $request_data = fn_cp_parse_form($page_data, $form_values);    
    }

    if (!empty($request_data)) {
        $request_data['to'] = $development_specialist_email; 
        $event_dispatcher->dispatch('cp_additional_email_templates.new_connection_request', [
            'request_data' => $request_data
        ]);

        $request_data['knowledge_base'] = $knowledge_base;
        $request_data['support_email'] = $development_specialist_email;
        $event_dispatcher->dispatch('cp_additional_email_templates.new_connection_request_sent', [
            'request_data' => $request_data
        ]);
    }
    
}
/* HOOKS */
function cp_additional_email_templates_install() 
{   

    $service = Tygh::$app['template.mail.service'];
    $email_data = array(
        array( 
            'code' => 'cp_additional_email_templates_new_connection_request', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_new_connection_request_subj") }}', 
            'addon' => 'cp_additional_email_templates', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_templates.please_check_request", {"[crm_href]" : request_data.crm_href})}} <br />
                {{ request_data.email }}
                {{ request_data.phone }}
                {{ request_data.user }}
                {{ request_data.company }}
                {{ request_data.inn }}
                {{ snippet("footer") }}',
        ),
        array( 
            'code' => 'cp_additional_email_templates_new_connection_request_sent', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_connection_request_sent_subj") }}', 
            'addon' => 'cp_additional_email_templates', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_templates.hello", {"[user]" : request_data.user})}} <br />
                {{__("cp_templates.info")}} <br />
                {{__("cp_templates.support_info", {"[knowledge_base]" : request_data.knowledge_base, "[support_email]" : request_data.support_email})}} <br />
                {{ snippet("footer") }}',
        ),
        array( 
            'code' => 'cp_additional_email_templates_order_unpaid', 
            'area' => 'C', 
            'status' => 'A', 
            'subject' => '{{__("cp_order_unpaid_subj")}}', 
            'addon' => 'cp_additional_email_templates', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_order_unpaid_info" , {"[order_id]" : request_data.order_id})}}
                {{ snippet("footer") }}',
        ),
        array( 
            'code' => 'cp_additional_email_templates_new_planned_time_issuing_order', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_new_planned_time_issuing_order_subj")}}', 
            'addon' => 'cp_additional_email_templates', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_new_planned_time_issuing_order_info" , {"[order_id]" : request_data.order_id, "[issuing_time]" : request_data.issuing_time})}}
                {{ snippet("footer") }}',
        ),
        array( 
        'code' => 'planned_time_issuing_order_change_for_customer', 
        'area' => 'C', 
        'status' => 'A', 
        'subject' => '{{__("cp_planned_time_change_for_customer_subj")}}', 
        'addon' => 'cp_additional_email_templates', 
        'template' => '
            {{ snippet("header") }}
            {{__("cp_planned_time_change_for_customer_info" , {"[order_id]" : request_data.order_id, "[issuing_time]" : request_data.issuing_time})}}
            <br />
            <a href="{{request_data.cancel_href}}">{{__("cp_oc_cancel_order")}}</a>
            {{ snippet("footer") }}',
        ),
        array( 
            'code' => 'planned_time_issuing_order_change_for_admin', 
            'area' => 'A', 
            'status' => 'A', 
            'subject' => '{{__("cp_planned_time_change_for_admin_subj")}}', 
            'addon' => 'cp_additional_email_templates', 
            'template' => '
                {{ snippet("header") }}
                {{__("cp_planned_time_change_for_admin_info" , {"[order_id]" : request_data.order_id, "[issuing_time]" : request_data.issuing_time})}}
                {{ snippet("footer") }}',
        )
    );

    fn_cp_create_notification_templates($email_data, $service); 
}
function fn_cp_additional_email_templates_uninstall() 
{ 
    $service = Tygh::$app['template.mail.service'];
    $email_data = array(
        'cp_additional_email_templates_new_connection_request',
        'cp_additional_email_templates_new_connection_request_sent',
        'cp_additional_email_templates_order_unpaid',
        'cp_additional_email_templates_new_planned_time_issuing_order',
        'planned_time_issuing_order_change_for_customer',
        'planned_time_issuing_order_change_for_admin'
    );
    fn_cp_delete_notification_templates($email_data, $service, "?:template_emails");
}
function fn_cp_create_notification_templates($template_data, $service)
{
    foreach ($template_data as $data) {
        $result = $service->createTemplate($data);
    } 
}
function fn_cp_delete_notification_templates($template_data, $service, $table)
{
    foreach ($template_data as $template_code) {
        $service->removeTemplateByCodeAndArea($template_code, 'A');      
    } 
    db_query('DELETE FROM '.$table.' WHERE area = ?s AND code IN (?a)', 'A', $template_data);
}
function fn_cp_parse_form($page_data, $form_values)
{   
    $request_data = array();
    
    foreach ($form_values as $input_id => $input_value) {
        $request_data[$page_data['form']['elements'][$input_id]['description']] = $input_value;
    }

    return fn_cp_find_email_fields($request_data);
}
function fn_cp_find_email_fields($request_data)
{   
    $email_request_data = array();

    foreach ($request_data as $description => $field_value) {
        
        switch (trim(mb_strtolower($description))) {
            case CP_FORM_COMPANY:
                $email_request_data['company'] = $field_value;
                break;
            case CP_FORM_INN:
                $email_request_data['inn'] = $field_value;
                break;
            case CP_FORM_USER:
                $email_request_data['user'] = $field_value;
                break;
            case CP_FORM_PHONE:
                $email_request_data['phone'] = $field_value;
                break;
            case CP_FORM_EMAIL:
                $email_request_data['email'] = $field_value;
                break;
            default:
                break;
        }
    }

    return $email_request_data;
}