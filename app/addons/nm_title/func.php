<?php


if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * Перехватывает необходимые формы и отправляет их в zoho
 * @param $page_data
 * @param $form_values
 * @param $result
 * @param $from
 * @param $sender
 * @param $attachments
 * @param $is_html
 * @param $subject
 */
function fn_nm_title_send_form(&$page_data, &$form_values, &$result, &$from, &$sender, &$attachments, &$is_html, &$subject)
{
    // create or find contact ID
    // assign ticket with contact
    // if isset attachs - send to zoho and assign with ticket
    // send ticket

    $parsing_form_names = [
        'Обратная связь', 'Стать поставщиком',
    ];
    if (!in_array($subject, $parsing_form_names))
        return true;


    $parsed_form = parse_form($page_data, $form_values);


    $uploads = [];
    $Zoho = new Zoho();
    $contact = $Zoho->createContact($parsed_form['contact_name'], $parsed_form['email'], $parsed_form['phone']);

    if (!isset($contact->id)) {
        $result = false;
    }


    if ($attachments)
        foreach ($attachments as $fileName => $filePath) {
            $folderPath = $_SERVER['DOCUMENT_ROOT'] . '/var/cache/misc/tmp/';
            $isRenamed = rename($filePath, $folderPath . $fileName);
            $pathToWork = $isRenamed ? $folderPath . $fileName : $filePath;
            $uploads[] = $Zoho->uploadFile($pathToWork);
            unlink($pathToWork);
        }


    switch ($subject) {
        case 'Обратная связь':
            $result = $Zoho->newQuestion(
                $contact->id,
                get_pretty_form($parsed_form['form']),
                $parsed_form['topic'],
                $parsed_form['phone'],
                $parsed_form['email'],
                $uploads
            );
            break;
        case 'Стать поставщиком':
            $result = $Zoho->newProvider(
                $contact->id,
                get_pretty_form($parsed_form['form']),
                $parsed_form['phone'],
                $parsed_form['email']
            );
            break;
    }

    $example = [
        '0' => [
            'page_id'              => 72,
            'company_id'           => 0,
            'parent_id'            => 70,
            'id_path'              => '70/72',
            'status'               => 'A',
            'page_type'            => 'F',
            'position'             => 0,
            'timestamp'            => 1598475600,
            'usergroup_ids'        => 0,
            'localization'         => '',
            'new_window'           => 0,
            'use_avail_period'     => 'N',
            'avail_from_timestamp' => 0,
            'avail_till_timestamp' => 0,
            'facebook_obj_type'    => '',
            'lang_code'            => 'ru',
            'page'                 => 'Ф-задать вопрос',
            'description'          => '',
            'meta_keywords'        => '',
            'meta_description'     => '',
            'page_title'           => '',
            'link'                 => '',
            'check_form_pd'        => 'N',
            'seo_name'             => 'f-zadat-vopros',
            'seo_path'             => 70,
            'form'                 => [
                'elements' => [
                    '1' => [
                        'element_id'   => 1,
                        'page_id'      => 72,
                        'parent_id'    => 0,
                        'element_type' => 'I',
                        'value'        => '',
                        'position'     => 0,
                        'required'     => 'Y',
                        'status'       => 'A',
                        'description'  => 'поле',
                    ],
                ],
                'general'  => [
                    'E' => '',
                    'K' => '',
                    'L' => '',
                    'J' => 'thezhenikuls@gmail . com',
                ],
            ],
        ],
        '1' => [
            '1' => 'sasq',
        ],
        '2' => 1,
        '3' => 'default_company_support_department',
        '4' => '',
        '5' => [
            '1036310.torrent' => '/home/testserviceparts/www/var/cache/misc/tmp/tmp_OTxJUz',
        ],
        '6' => 1,
        '7' => 'Ф - задать вопрос',
    ];
}

function fn_nm_title_cp_crm_process_payments($order_id, $data, $status_answer)
{
    if ($status_answer == 200) { // Send payment in ZOHO only in not OK status
        return true;
    }
    $Zoho = new Zoho();

    $contact = $Zoho->createContact($data['customer_name'], 'noemail@service.parts');

    if (!isset($contact->id)) {
        return false;
    }

    $data['payment_order_date'] =
        DateTime::createFromFormat('Y-m-d\TH:i:s', $data['payment_order_date'])->format('Y-m-d H:i');


    $data['order_id'] = $order_id;
    $data['link'] =
        "<a target=\"_blank\"
         href=\"https://${$_SERVER['SERVER_NAME']}/YmU6HrW91fuwciuN.php?dispatch=orders.details&order_id=" . $order_id . "\">Открыть</a>";
    translate_form($data);

    $Zoho->newPayment($contact->id, get_pretty_form($data), $order_id);

}

function parse_form($page_data, $form_values)
{
    $keys = [];
    $options = [];
    $results = [
        'form'         => null,
        'email'        => null,
        'phone'        => null,
        'contact_name' => null,
        'topic'        => null,
    ];
    $form = [];

    foreach ($page_data['form']['elements'] as $element) {
        $keys[$element['element_id']] = $element['description'];
        if (isset($element['variants'])) {
            foreach ($element['variants'] as $variant) {
                $options[$element['element_id']][$variant['element_id']] = $variant['description'];
            }
        }
    }

    foreach ($form_values as $input_key => $form_value) {
        if (!$form_value) continue;
        $value_key = explode('_', $input_key)[0];
        if (isset($options[$value_key]) && isset($options[$value_key][$form_value])) { // if is select
            $form[$keys[$value_key]] = $options[$value_key][$form_value];
            continue;
        }
        $form[$keys[$value_key]] = $form_value;
    }

    foreach ($form as $key => $value) {
        if (mb_strpos(mb_strtolower($key), 'email') !== false)
            $results['email'] = $value;
        if (mb_strpos(mb_strtolower($key), 'телефон') !== false)
            $results['phone'] = $value;
        if (mb_strpos(mb_strtolower($key), 'как к вам обращаться') !== false)
            $results['contact_name'] = $value;
        if (mb_strpos(mb_strtolower($key), 'контактное лицо') !== false)
            $results['contact_name'] = $value;
        if (mb_strpos(mb_strtolower($key), 'тема') !== false)
            $results['topic'] = $value;
    }

    $results['form'] = $form;

    return $results;
}

function get_pretty_form($form)
{
    $pretty = '';
    foreach ($form as $key => $value) {
        $pretty .= $key . ' — ' . $value . "\r\n<br>";
    }
    return $pretty;
}

function translate_form(array &$form)
{
    $newForm = [];
    $basic = [
        'payment_order_date'   => 'Дата платежного поручения',
        'payment_order_number' => 'Номер платежного поручения',
        'bank_account'         => 'Банковский счёт',
        'bank_bic'             => 'БИК',
        'payment_purpose'      => 'Назначение платежа',
        'customer_tin'         => 'customer_tin',
        'customer_name'        => 'Имя покупателя',
        'currency_id'          => 'ID валюты',
        'currency_name'        => 'Валюта',
        'amount_total'         => 'Общая сумма',
        'amount_vat'           => 'Сумма, НДС',
        'order_id'             => 'Номер заказа на сайте',
        'link'                 => 'CS-cart',
    ];
    foreach ($form as $key => $value) {
        $newForm[$basic[$key] ?? $key] = $value;
    }
    $form = $newForm;
}

function fn_link_to_get_tokens_info(){
    $href = $_SERVER['DOCUMENT_URI'].'?dispatch=nm_title.zohoRequestCode';
    return "<b>Должны быть заполнены первые два поля перед активацией</b><br><b>Ссылка на первоначальную активацию: <a target=\"_blank\" href='{$href}'>Здесь</a></b>";
}