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

if (is_file(Registry::get('config.dir.schemas') . 'exim/product_combinations.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/product_combinations.functions.php');
}

if (is_file(Registry::get('config.dir.schemas') . 'exim/products.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/products.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/qty_discounts.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/qty_discounts.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/option_exceptions.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/option_exceptions.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/features.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/features.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/feature_variants.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/feature_variants.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/language_variables.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/language_variables.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/order_items.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/order_items.functions.php');
}
if (is_file(Registry::get('config.dir.schemas') . 'exim/users.functions.php')) {
    include_once(Registry::get('config.dir.schemas') . 'exim/users.functions.php');
}

$mode = '';
include_once(Registry::get('config.dir.root') . '/app/controllers/backend/exim.php');

if (!defined('BOOTSTRAP')) { die('Access denied'); }

define('DB_TM_LIMIT_SELECT_ROW', 30);

function fn_cp_task_manager_import($pattern, $import_data, $options)
{
    if (empty($pattern) || empty($import_data)) {
        return false;
    }

    $processed_data = array (
        'E' => 0, // existent
        'N' => 0, // new
        'S' => 0, // skipped
    );

    $alt_keys = array();
    $primary_fields = array();
    $table_groups = array();
    $default_groups = array();
    $add_fields = array();
    $primary_object_ids = array();
    $required_fields = array();
    $alt_fields = array();
    if (!empty($pattern['pre_processing'])) {
        $data_pre_processing = array(
            'import_data' => &$import_data,
            'pattern' => &$pattern,
        );
        fn_exim_processing('import', $pattern['pre_processing'], $options, $data_pre_processing);
    }

    if (!empty($pattern['references'])) {
        $table_groups =  $pattern['references'];
    }

    if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
        if (!fn_import_parse_languages($pattern, $import_data, $options)) {
            //fn_set_notification('E', __('error'), __('error_exim_invalid_count_langs'));

            return false;
        }
    } else {
        if (!fn_exim_import_parse_languages($pattern, $import_data, $options)) {
            //fn_set_notification('E', __('error'), __('error_exim_invalid_count_langs'));

            return false;
        }
    }

    // Get keys to detect primary record
    foreach ($pattern['export_fields'] as $field => $data) {

        $_db_field = (empty($data['db_field']) ? $field : $data['db_field']);

        // Collect fields with default values
        if (isset($data['default'])) {
            if (is_array($data['default'])) {
                $default_groups[$_db_field] = call_user_func_array(array_shift($data['default']), $data['default']);
            } else {
                $default_groups[$_db_field] = $data['default'];
            }
        }

        // Get alt keys for primary table
        if (!empty($data['alt_key'])) {
            $alt_keys[$field] = $_db_field;
        }

        if (!empty($data['alt_field'])) {
            $alt_fields[$_db_field] = $data['alt_field'];
        }

        if (!empty($data['required']) && $data['required'] = true) {
            $required_fields[] = $_db_field;
        }

        if (!isset($data['linked']) || $data['linked'] == true) {
            // Get fields for primary table
            if (empty($data['table']) || $data['table'] == $pattern['table']) {
                $primary_fields[$field] = $_db_field;
            }

            // Group fields by tables
            if (!empty($data['table'])) {
                $table_groups[$data['table']]['fields'][$_db_field] = true;
            }
        }

        // Create set with fields that must be added to data import if they are not exist
        // %'s are for compatibility with %% field type in "process_put" key
        if (!empty($data['use_put_from'])) {
            $_f = str_replace('%', '', $data['use_put_from']);
            $_f = !empty($pattern['export_fields'][$_f]['db_field']) ? $pattern['export_fields'][$_f]['db_field'] : $_f;
            $add_fields[$_f][] = $_db_field;
        }
    }

    // Generate processing groups
    if (function_exists('fn_import_build_groups')) {
        $processing_groups = fn_import_build_groups('process_put', $pattern['export_fields']);

        // Generate converting groups
        $converting_groups = fn_import_build_groups('convert_put', $pattern['export_fields']);

        //Generate pre inserting groups
        $pre_inserting_groups = fn_import_build_groups('pre_insert', $pattern['export_fields']);

        //Generate post inserting groups
        $post_inserting_groups = fn_import_build_groups('post_insert', $pattern['export_fields']);
    } else {
        $processing_groups = fn_exim_import_build_groups('process_put', $pattern['export_fields']);

        // Generate converting groups
        $converting_groups = fn_exim_import_build_groups('convert_put', $pattern['export_fields']);

        //Generate pre inserting groups
        $pre_inserting_groups = fn_exim_import_build_groups('pre_insert', $pattern['export_fields']);

        //Generate post inserting groups
        $post_inserting_groups = fn_exim_import_build_groups('post_insert', $pattern['export_fields']);
    }

    //fn_set_progress('parts', sizeof($import_data));

    $data = reset($import_data);
    $multi_lang = array_keys($data);
    $main_lang = reset($multi_lang);

    foreach ($import_data as $k => $v) {

        //If the required field is empty skip this record
        foreach ($required_fields as $field) {
            if (empty($v[$main_lang][$field]) && $v[$main_lang][$field] !== 0) {
                if (empty($alt_fields[$field]) || empty($v[$main_lang][$alt_fields[$field]])) {
                    $processed_data['S']++;
                    continue 2;
                }
            }
        }

        $_alt_keys = array();
        $object_exists = true;

        // Check if converting groups exist and convert fields if it is so
        if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
            fn_import_prepare_groups($v[$main_lang], $converting_groups, $options);
        } else {
            fn_exim_import_prepare_groups($v[$main_lang], $converting_groups, $options);
        }

        foreach ($alt_keys as $import_field => $real_field) {
            if (!isset($v[$main_lang][$real_field])) {
                continue;
            }
            if (!empty($v[$main_lang][$real_field])) {
                $_alt_keys[$real_field] = $v[$main_lang][$real_field];
            } elseif (!empty($alt_fields[$real_field])) {
                $_alt_keys[$alt_fields[$real_field]] = $v[$main_lang][$alt_fields[$real_field]];
            }

        }

        foreach ($primary_fields as $import_field => $real_field) {
            if (!isset($v[$main_lang][$real_field])) {
                continue;
            }
            $_primary_fields[$real_field] = $v[$main_lang][$real_field];
        }

        $skip_get_primary_object_id = false;

        if (!empty($pattern['import_get_primary_object_id'])) {
            $data_import_get_primary_object_id = array(
                'pattern' => &$pattern,
                'alt_keys' => &$_alt_keys,
                'object' => &$v[$main_lang],
                'skip_get_primary_object_id' => &$skip_get_primary_object_id,
            );

            fn_exim_processing('import', $pattern['import_get_primary_object_id'], $options, $data_import_get_primary_object_id);
        }
        if ($skip_get_primary_object_id) {
            $primary_object_id = array();
        } else {
            $where = array();
            foreach ($_alt_keys as $field => $value) {
                $where[] = db_quote("?p = ?s", $field, $value);
            }
            $where = implode(' AND ', $where);

            $primary_object_id = db_get_row('SELECT ' . implode(', ', $pattern['key']) . ' FROM ?:' . $pattern['table'] . ' WHERE ?p', $where);
        }
        $primary_object_ids[] = $primary_object_id;
        $skip_record = false;
        if (!empty($pattern['import_process_data'])) {
            $data_import_process_data = array(
                'primary_object_id' => &$primary_object_id,
                'object' => &$v[$main_lang],
                'pattern' => &$pattern,
                'options' => &$options,
                'processed_data' => &$processed_data,
                'processing_groups' => &$processing_groups,
                'skip_record' => &$skip_record,
                'data' =>&$v,
            );

            fn_exim_processing('import', $pattern['import_process_data'], $options, $data_import_process_data);
        }
        if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
            fn_import_prepare_groups($v[$main_lang], $pre_inserting_groups, $options, $skip_record);
        } else {
            fn_exim_import_prepare_groups($v[$main_lang], $pre_inserting_groups, $options, $skip_record);
        }

        if ($skip_record) {
            continue;
        }

        if (!(isset($pattern['import_skip_db_processing']) && $pattern['import_skip_db_processing'])) {

            if (empty($primary_object_id)) {

                // If scheme is used for update objects only, skip this record
                if (!empty($pattern['update_only'])) {
                    $_a = array();
                    foreach ($alt_keys as $_d => $_v) {
                        if (!isset($v[$main_lang][$_v])) {
                            continue;
                        }
                        $_a[] = $_d . ' = ' . $v[$main_lang][$_v];
                    }

                    $processed_data['S']++;
                    continue;
                }

                $object_exists = false;
                $processed_data['N']++;

                // For new objects - fill the default values
                if (!empty($default_groups)) {
                    foreach ($default_groups as $field => $value) {
                        if (empty($v[$main_lang][$field])) {
                            $v[$main_lang][$field] = $value;
                        }
                    }
                }
            } else {
                $processed_data['E']++;
            }

            if ($object_exists == true) {
                db_query('UPDATE ?:' . $pattern['table'] . ' SET ?u WHERE ?w', $v[$main_lang], $primary_object_id);
            } else {
                $o_id = db_query('INSERT INTO ?:' . $pattern['table'] . ' ?e', $v[$main_lang]);

                if ($o_id !== true) {
                    $primary_object_id = array(reset($pattern['key']) => $o_id);
                } else {
                    foreach ($pattern['key'] as $_v) {
                        $primary_object_id[$_v] = $v[$main_lang][$_v];
                    }
                }

            }
        }

        $skip_db_processing_record = false;
        
        if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
            fn_import_prepare_groups($v[$main_lang], $post_inserting_groups, $options, $skip_db_processing_record);
        } else {
            fn_exim_import_prepare_groups($v[$main_lang], $post_inserting_groups, $options, $skip_db_processing_record);
        }
        if (!empty($pattern['import_after_process_data'])) {
            $data_import_after_process_data = array(
                'primary_object_id' => &$primary_object_id,
                'object' => &$v[$main_lang],
                'pattern' => &$pattern,
                'options' => &$options,
                'processed_data' => &$processed_data,
                'processing_groups' => &$processing_groups,
                'skip_db_processing_record' => &$skip_db_processing_record,
            );

            fn_exim_processing('import', $pattern['import_after_process_data'], $options, $data_import_after_process_data);
        }
        if ($skip_db_processing_record) {
            continue;
        }



        if (!(isset($pattern['import_skip_db_processing']) && $pattern['import_skip_db_processing'])) {
            // Update referenced tables
            foreach ($table_groups as $table => $tdata) {
                if (isset($tdata['import_skip_db_processing']) && $tdata['import_skip_db_processing']) {
                    continue;
                }

                foreach ($v as $value_data) {
                    $_data = array();

                    // First, build condition
                    $where_insert = array();

                    // If alternative key is defined, use it
                    if (!empty($tdata['alt_key'])) {

                        foreach ($tdata['alt_key'] as $akey) {
                            if (strval($akey) == '#key') {
                                $where_insert = fn_array_merge($where_insert, $primary_object_id);
                            } elseif (strpos($akey, '@') !== false) {
                                $_opt = str_replace('@', '', $akey);
                                $where_insert[$akey] = $options[$_opt];
                            } else {
                                $where_insert[$akey] = $value_data[$akey];
                            }
                        }
                    // Otherwise - link by reference fields
                    } else {
                        $vars = array('key' => $primary_object_id);
                        if (!empty($value_data['lang_code'])) {
                            $vars['lang_code'] = $value_data['lang_code'];
                        }
                        $where_insert = fn_exim_get_values($tdata['reference_fields'], array(), $options, $vars, $value_data, '');
                    }

                    // Now, build update fields array
                    if (!empty($tdata['fields'])) {
                        foreach ($tdata['fields'] as $import_field => $set) {
                            if (!isset($value_data[$import_field])) {
                                continue;
                            }
                            $_data[$import_field] = $value_data[$import_field];
                        }
                    }

                    // Check if object exists
                    $is_exists = db_get_field("SELECT COUNT(*) FROM ?:$table WHERE ?w", $where_insert);
                    if ($is_exists == true && !empty($_data)) {
                        db_query("UPDATE ?:$table SET ?u WHERE ?w", $_data, $where_insert);
                    } elseif (empty($is_exists)) { // if reference does not exist, we should insert it anyway to avoid inconsistency
                        $_data = fn_array_merge($_data, $where_insert);

                        if (substr($table, -13) == '_descriptions' && isset($_data['lang_code'])) {
                            // add description for all cart languages when adding object data
                            foreach (fn_get_translation_languages() as $_data['lang_code'] => $lang_v) {
                                db_query("REPLACE INTO ?:$table ?e", $_data);
                            }

                        } else {
                            db_query("INSERT INTO ?:$table ?e", $_data);
                        }
                    }

                    //
                    if (empty($_data['lang_code'])) {
                        break;
                    }
                }
            }
        }

        if (!empty($processing_groups)) {

            foreach ($processing_groups as $group) {

                $args = array();
                $use_this_group = true;
                $_refs = array();

                foreach ($group['args'] as $ak => $av) {

                    foreach ($v as $lang_code => $value) {
                        if ($av == '#key') {
                            $args[$ak] = (sizeof($primary_object_id) >= 1) ? reset($primary_object_id) : $primary_object_id;

                        } elseif ($av == '#keys') {
                            $args[$ak] = is_array($primary_object_id) ? $primary_object_id : (array) $primary_object_id;

                        } elseif ($av == '#new') {
                            $args[$ak] = !$object_exists;

                        } elseif ($av == '#lang_code') {
                            $args[$ak] = $lang_code;

                        } elseif ($av == '#row') {
                            $args[$ak] = $value;

                        } elseif ($av == '#this') {
                            // If we do not have this field in the import data, do not apply the function
                            $this_id = $group['this_field'];

                            if (!isset($value[$this_id])) {
                                $is_empty_data = true;

                                if (!empty($add_fields[$this_id])) {
                                    foreach ($add_fields[$this_id] as $from_field) {
                                        if (isset($value[$from_field])) {
                                            $is_empty_data = false;
                                        }
                                    }
                                }

                                if ($is_empty_data) {
                                    $use_this_group = false;
                                    break;
                                }
                            }

                            $this_multilang = false;

                            if (!empty($pattern['export_fields'][$this_id]['multilang'])) {
                                $this_multilang = true;
                            } else {
                                foreach ($pattern['export_fields'] as $field) {
                                    if (!empty($field['multilang']) && !empty($field['db_field']) && $field['db_field'] == $this_id) {
                                        $this_multilang = true;
                                        break;
                                    }
                                }
                            }

                            if ($this_multilang) {
                                $args[$ak][$lang_code] = $value[$group['this_field']];
                            } else {
                                $args[$ak] = $value[$group['this_field']];
                                break;
                            }

                        } elseif ($av == '#counter') {
                            $args[$ak] = &$processed_data;

                        } elseif (strpos($av, '%') !== false) {
                            $_ref = str_replace('%', '', $av);
                            $arg_multilang = !empty($pattern['export_fields'][$_ref]['multilang']);
                            $_ref = !empty($pattern['export_fields'][$_ref]['db_field']) ? $pattern['export_fields'][$_ref]['db_field'] : $_ref; // FIXME!!! Move to code, which builds processing_groups

                            if ($arg_multilang) {
                                $args[$ak][$lang_code] = isset($value[$_ref]) ? $value[$_ref] : '';
                            } elseif ($lang_code == $main_lang) {
                                $args[$ak] = isset($value[$_ref]) ? $value[$_ref] : '';
                            }

                            $_refs[$lang_code][] = $_ref;

                        } elseif (strpos($av, '@') !== false) {
                            $_opt = str_replace('@', '', $av);
                            $args[$ak] = $options[$_opt];

                        } else {
                            $args[$ak] = $av;
                        }

                        if (empty($group['multilang'])) {
                            break;
                        }
                    }

                }

                if ($use_this_group == false) {
                    continue;
                }

                $result = call_user_func_array($group['function'], $args); // FIXME - add checking for returned value
                if (version_compare(PRODUCT_VERSION, '4.7.1', '>')) {
                    if ($group['return_result'] == true) {
                        foreach (array_keys($v) as $lang) {
                            $v[$lang][$group['return_field']] = $result;
                            $import_data[$k][$lang][$group['return_field']] = $result;
                        }
                    }
                } else {
                    if ($group['return_result'] == true) {
                        foreach (array_keys($v) as $lang) {
                            $v[$lang][$group['this_field']] = $result;
                        }
                    }
                }
            }
        }
    }
    $final_import_notification = '';
    if (version_compare(PRODUCT_VERSION, '4.7.1', '>')) {
        if (!empty($pattern['post_processing'])) {
            $data_post_processing = [
                'primary_object_ids'        => &$primary_object_ids,
                'import_data'               => &$import_data,
                'processed_data'            => &$processed_data,
                'final_import_notification' => &$final_import_notification,
                'pattern'                   => &$pattern
            ];

            fn_exim_processing('import', $pattern['post_processing'], $options, $data_post_processing);
        }
    } else {
        if (!empty($pattern['post_processing'])) {
            $data_post_processing = array(
                'primary_object_ids' => &$primary_object_ids,
                'import_data' => &$import_data,
            );
            fn_exim_processing('import', $pattern['post_processing'], $options, $data_post_processing);
        }
    }
    
    return __('text_exim_data_imported', array(
         '[new]' => $processed_data['N'],
         '[exist]' => $processed_data['E'],
         '[skipped]' => $processed_data['S'],
         '[total]' => $processed_data['E'] + $processed_data['N'] + $processed_data['S']
    ));
}


function fn_cp_task_manager_export($pattern, $export_fields, $options)
{
    if (empty($pattern) || empty($export_fields)) {
        return false;
    }

    // Languages
    if (!empty($options['lang_code'])) {
        $multi_lang = $options['lang_code'];
        $count_langs = count($multi_lang);
    } else {
        $multi_lang = array(DEFAULT_LANGUAGE);
        $count_langs = 1;
        $options['lang_code'] = $multi_lang;
    }

    $can_continue = true;

    if (!empty($pattern['export_pre_moderation'])) {
        $data_export_pre_moderation = array(
            'pattern' => &$pattern,
            'export_fields' => &$export_fields,
            'options' => &$options,
            'can_continue' => &$can_continue,
        );

        fn_exim_processing('export', $pattern['export_pre_moderation'], $options, $data_export_pre_moderation);
    }

    if (!$can_continue) {
        return false;
    }

    if (!empty($pattern['pre_processing'])) {
        fn_exim_processing('export', $pattern['pre_processing'], $options);
    }

    if (isset($options['fields_names'])) {
        if ($options['fields_names']) {
            $fields_names = $export_fields;
            $export_fields = array_keys($export_fields);
        }
    }

    $primary_key = array();
    $_primary_key = $pattern['key'];
    foreach ($_primary_key as $key) {
        $primary_key[$key] = $key;
    }
    
    if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
        array_walk($primary_key, 'fn_attach_value_helper', $pattern['table'].'.');
    } else {
        array_walk($primary_key, 'fn_exim_attach_value_helper', $pattern['table'].'.');
    }

    $table_fields = $primary_key;
    $processes = array();

    // Build list of fields that should be retrieved from the database
    if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
        fn_export_build_retrieved_fields($processes, $table_fields, $pattern, $export_fields);
    } else {
        fn_exim_export_build_retrieved_fields($processes, $table_fields, $pattern, $export_fields);
    }

    if (empty($pattern['export_fields']['multilang'])) {
        $multi_lang = array(DEFAULT_LANGUAGE);
        $count_langs = 1;
        $options['lang_code'] = $multi_lang;
    }

    // Build the list of joins
    if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
        $joins = fn_export_build_joins($pattern, $options, $primary_key, $multi_lang);
    } else {
        $joins = fn_exim_export_build_joins($pattern, $options, $primary_key, $multi_lang);
    }

    // Add retrieve conditions
    if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
        $conditions = fn_export_build_conditions($pattern, $options);
    } else {
        $conditions = fn_exim_export_build_conditions($pattern, $options);
    }

    if (!empty($pattern['pre_export_process'])) {
        $pre_export_process_data = array(
            'pattern' => &$pattern,
            'export_fields' => &$export_fields,
            'options' => &$options,
            'conditions' => &$conditions,
            'joins' => &$joins,
            'table_fields' => &$table_fields,
            'processes' => &$processes
        );
        fn_exim_processing('export', $pattern['pre_export_process'], $options, $pre_export_process_data);
    }
    if (fn_allowed_for('MULTIVENDOR') && !empty($options['company_id']) && !empty($pattern['condition']) && !empty($pattern['condition']['use_company_condition'])) {
        $conditions[]= db_quote($pattern['table'] . '.company_id = ?s', $options['company_id']);
    }
    $total = db_get_field("SELECT COUNT(*) FROM ?:" . $pattern['table'] . " as " . $pattern['table'] .' '. implode(' ', $joins) . (!empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : ''));

    $sorting = '';

    if (!empty($pattern['order_by'])) {
        $sorting = ' ORDER BY ' . $pattern['order_by'];
    }

    // Build main query
    $query = "SELECT " . implode(', ', $table_fields) . " FROM ?:" . $pattern['table'] . " as " . $pattern['table'] .' '. implode(' ', $joins) . (!empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '') . $sorting;

    $step = fn_floor_to_step(DB_TM_LIMIT_SELECT_ROW,  $count_langs); // define number of rows to get from database
    $iterator = 0; // start retrieving from
    $progress = 0;
    $data_exported = false;

    $main_lang = reset($multi_lang);
    $manual_multilang = true;

    $field_lang = '';

    foreach ($pattern['export_fields']['multilang'] as $key => $value) {
        if (array_search('languages', $value, true)) {
            if (!isset($value['linked']) || $value['linked'] === true) {
                $manual_multilang = false;
            }
            $field_lang = $key;

            break;
        }
    }

    if (empty($field_lang) || !in_array($field_lang, $export_fields)) {
        $multi_lang = array($main_lang);
        $count_langs = 1;
    }

    while ($data = db_get_array($query . " LIMIT $iterator, $step")) {
        $data_exported = true;

        if ($manual_multilang) {
            $data_lang = $data;
            $data = array();

            foreach ($data_lang as $data_key => $data_value) {
                $data[] = array_combine($multi_lang, array_fill(0, $count_langs, $data_value));
            }

        } else {

            $data_lang = array_chunk($data, $count_langs);
            $data = array();

            foreach ($data_lang as $data_key => $data_value) {
                // Sort
                foreach ($multi_lang as $lang_code) {
                    foreach ($data_value as $v) {
                        if (array_search($lang_code, $v, true)) {
                            $data[$data_key][$lang_code] = $v;
                        }
                    }
                }
            }
        }

        $result = array();
        foreach ($data as $k => $v) {
            $progress += $count_langs;
            if (version_compare(PRODUCT_VERSION, '4.4', '<')) {
                fn_export_fill_fields($result[$k], $v, $processes, $pattern, $options);
            } else {
                fn_exim_export_fill_fields($result[$k], $v, $processes, $pattern, $options);
            }

        }

        $_result = array();

        foreach ($result as $k => $v) {
            foreach ($multi_lang as $lang_code) {
                $_data = array();
                foreach ($export_fields as $field) {
                    if (isset($fields_names[$field])) {
                        $_data[$fields_names[$field]] = $v[$lang_code][$field];
                    } else {
                        $_data[$field] = (isset($v[$lang_code][$field])) ? $v[$lang_code][$field] : '';
                    }
                }
                $_result[] = $_data;
            }
        }

        // Put data
        $enclosure = (isset($pattern['enclosure'])) ? $pattern['enclosure'] : '"';

        if (isset($pattern['func_save_content_to_file']) && is_callable($pattern['func_save_content_to_file'])) {
            call_user_func($pattern['func_save_content_to_file'], $_result, $options, $enclosure);
        } else {
            if (fn_allowed_for('MULTIVENDOR') && !empty($options['company_id']) && !empty($pattern['condition']) && !empty($pattern['condition']['use_company_condition'])) {
                $options['filename'] = $options['company_id'] . '/' . $options['filename'];
            }
            if (function_exists('fn_put_csv')) {
                fn_put_csv($_result, $options, $enclosure);
            } else {
                fn_exim_put_csv($_result, $options, $enclosure);
            }
        }

        $iterator += $step;
    }

    
    if (!empty($pattern['post_processing'])) {

        if ($data_exported && file_exists(fn_get_files_dir_path() . $options['filename'])) {

            $data_exported = fn_exim_processing('export', $pattern['post_processing'], $options);
        }
    }

    return $data_exported;
}


function fn_cp_task_manager_import_parse_languages($pattern, &$import_data, $options)
{

    foreach ($pattern['export_fields'] as $field_name => $field) {
        if (!empty($field['type']) && $field['type'] == 'languages') {
            if (empty($field['db_field'])) {
                $field_lang = $field_name;
            } else {
                $field_lang = $field['db_field'];
            }
        }
    }

    // Languages
    $langs = array();

    // Get all lang from data
    foreach ($import_data as $k => $v) {
        if (!isset($v['lang_code']) || in_array($v['lang_code'], $langs)) {
            break;
        }
        $langs[] = $v['lang_code'];
    }

    if (empty($langs)) {
        foreach ($import_data as $key => $data) {
            $import_data[$key]['lang_code'] = DEFAULT_LANGUAGE;
        }

        $langs[] = DEFAULT_LANGUAGE;
    }

    $langs = array_intersect($langs, array_keys(fn_get_translation_languages()));
    $count_langs = count($langs);

    $count_lang_data = array();
    foreach ($langs as $lang) {
        $count_lang_data[$lang] = 0;
    }

    $data = array();
    $result = true;
    if (isset($field_lang)) {
        foreach ($import_data as $v) {
            if (!empty($v[$field_lang]) && in_array($v[$field_lang], $langs)) {
                $data[] = $v;
                $count_lang_data[$v[$field_lang]]++;
            }
        }

        // Check
        $count_data = reset($count_lang_data);
        foreach ($langs as $lang) {
            if ($count_lang_data[$lang] != $count_data) {
                $result = false;
                break;
            }
        }

        if ($result) {
            // Chunk on languages
            $data_lang = array_chunk($data, $count_langs);
            $data = array();

            foreach ($data_lang as $data_key => $data_value) {
                foreach ($data_value as $v) {
                    $data[$data_key][$v[$field_lang]] = $v;
                }
            }

            if (fn_allowed_for('ULTIMATE')) {
                foreach ($data as $data_key => $data_value) {
                    $data_main = array_shift($data_value);
                    if (empty($data_main['company'])) {
                        $data_main['company'] = Registry::get('runtime.company_data.company');
                    }
                    foreach ($data_value as $v) {
                        $data[$data_key][$v[$field_lang]]['company'] = $data_main['company'];
                    }
                }
            }

            $import_data = $data;
        }
    } else {
        $main_lang = reset($langs);
        foreach ($import_data as $data_key => $data_value) {
            $data[$data_key][$main_lang] = $data_value;
        }
        $import_data = $data;
    }

    return $result;
}