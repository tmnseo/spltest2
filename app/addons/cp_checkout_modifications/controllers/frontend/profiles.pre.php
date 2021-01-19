<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode === 'update') {
        
        $user_data = & $_REQUEST['user_data'];
        $profile_id = empty($_REQUEST['profile_id']) ? 0 : $_REQUEST['profile_id'];
        
        // store actual address as billing address
        if (empty($profile_id) && isset($_REQUEST['actual_same_as_billing']) && $_REQUEST['actual_same_as_billing'] ==  true) {
            
            $actual_address_fields = db_get_hash_single_array("SELECT field_id, field_name FROM ?:profile_fields WHERE field_name LIKE ?s", array('field_name', 'field_id'), 'a\_%');
            $billing_address_fields = db_get_hash_single_array("SELECT field_id, field_name FROM ?:profile_fields WHERE field_name LIKE ?s", array('field_name', 'field_id'), 'b\_%');
            
            foreach ($actual_address_fields as $field_name => $field_id) {
                
                $b_field = str_replace('a_', 'b_', $field_name);
                
                if (!empty($user_data[$b_field])) {
                    
                    $user_data['fields'][$field_id] = $user_data[$b_field];
                }
                elseif (!empty($user_data['fields'])  && !empty($user_data['fields'][$billing_address_fields[$b_field]])) {
                    
                    $user_data['fields'][$field_id] = $user_data['fields'][$billing_address_fields[$b_field]];
                }
            }
        /*matching address*/
        }
        /*matching address*/
        if (!empty($auth['user_id']) && isset($_REQUEST['actual_same_as_billing']) && $_REQUEST['actual_same_as_billing'] ==  true ) {
            db_query("UPDATE ?:users SET `cp_is_matching_addresses` = ?s WHERE `user_id` = ?i", 'Y', $auth['user_id']);
        }elseif (!empty($auth['user_id']) && isset($_REQUEST['actual_same_as_billing']) && $_REQUEST['actual_same_as_billing'] == false){
            db_query("UPDATE ?:users SET `cp_is_matching_addresses` = ?s WHERE `user_id` = ?i", 'N', $auth['user_id']);
        }
        /*matching address*/
        
        /*
        if (empty($profile_id)) {
            $profile_id = db_get_field("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_type = 'P'", $auth['user_id']);
        }
        */
        
        if (!empty($auth['user_id']) && !empty($profile_id)) {
            
            $old_user_data = fn_get_user_info($auth['user_id'], true, $profile_id);
            
            if (!empty($user_data)) {
            
                $restored_fields = db_get_hash_single_array("SELECT field_id, field_name FROM ?:profile_fields WHERE cp_edited_only_by_admin = ?s", array('field_id', 'field_name'), 'Y');
                
                foreach ($restored_fields as $field_id => $field_name) {
                    
                    if (!empty($user_data[$field_name])) {
                        if (!empty($old_user_data[$field_name])) {
                            $user_data[$field_name] = $old_user_data[$field_name];
                        }
                        else {
                            unset($user_data[$field_name]);
                        }
                    }
                    elseif (!empty($user_data['fields'])  && !empty($user_data['fields'][$field_id])) {
                        if (!empty($old_user_data['fields'][$field_id])) {
                            $user_data['fields'][$field_id] = $old_user_data['fields'][$field_id];
                        }
                        else {
                            unset($user_data['fields'][$field_id]);
                        }
                    }
                }
            }
        }
    }
}