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

use Tygh\Addons\CpEmailParser\PhpImap\Mailbox as ImapMailbox;
use Tygh\Addons\CpEmailParser\PhpImap\Imap;
use Tygh\Addons\CpEmailParser\ServiceProvider;
use Tygh\Addons\CpEmailParser\Log\Logger;
use Tygh\Addons\CpEmailParser\Log\LogTypesEnum;
use Tygh\Addons\CpEmailParser\Log\LogManager;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $redirect_url = "";

    if ($mode == 'clean_logs') {

        LogManager::clearLogs();
        $redirect_url = "cp_email_parser.parsing_logs";

    }elseif ($mode == 'clean_old_logs') {

        LogManager::clearOldLogs();
        $redirect_url = "cp_email_parser.parsing_logs";

    }

    return [CONTROLLER_STATUS_OK, $redirect_url];
}
if ($mode == 'parsing_manually') {

    $cron_pass = ServiceProvider::cronPass();
    $cron_params = "?cron_pass=" . $cron_pass . "&is_manually=1";

    return [CONTROLLER_STATUS_REDIRECT, 'cp_email_parser.parse_email' . $cron_params];

}elseif ($mode == 'parse_email') {

    $cron_pass = ServiceProvider::cronPass();
    $main_logger = new Logger();

    if (empty($_REQUEST['cron_pass']) || empty($cron_pass) || $cron_pass != $_REQUEST['cron_pass']) {
        
        $main_logger->setError(LogTypesEnum::ERROR_CRON_PASS);
        $main_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_CRON_PASS));
        $main_logger->finishLog();

        if (isset($_REQUEST['is_manually'])) {
            return [CONTROLLER_STATUS_REDIRECT, 'cp_email_parser.parsing_logs'];
        }

        die('Access denied');
    }
    
    $dir_result = fn_mkdir(ServiceProvider::mailParserFilesDirectory(), 0777);
    if (!$dir_result) {

        $main_logger->setError(LogTypesEnum::ERROR_DIR_CREATE);
        $main_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_DIR_CREATE));
        $main_logger->finishLog();

        if (isset($_REQUEST['is_manually'])) {
            return [CONTROLLER_STATUS_REDIRECT, 'cp_email_parser.parsing_logs'];
        }
    }

    $host = ServiceProvider::HOST;
    $username = ServiceProvider::mailUser();
    $password = ServiceProvider::mailPassword();
    
    $can_connect = imap_open($host, $username, $password); //check connect
    
    if(!$can_connect) {
        $main_logger->setError(LogTypesEnum::ERROR_IMAP_CONNECT);
        $main_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_IMAP_CONNECT));
        $main_logger->finishLog();

        if (isset($_REQUEST['is_manually'])) {
            return [CONTROLLER_STATUS_REDIRECT, 'cp_email_parser.parsing_logs'];
        }

    }
    
    $mailbox = new ImapMailbox($host, $username, $password, ServiceProvider::mailParserFilesDirectory());
    
    $messages_ids = $mailbox->searchMailBox("UNSEEN");
    
    $count_messages = 0;
    $count_files = 0;

    foreach ($messages_ids as $_mid) {

        $count_messages++;

        $message_logger = new Logger();
        $message_logger->setParentLogId($main_logger->getLogId());
        $message_logger->setType(LogTypesEnum::MESSAGE);
    
        $parser = ServiceProvider::getMailParser();

        $mailbox->markMailAsRead($_mid);

        $mail_header = $mailbox->getMailHeader($_mid);
        if (empty($mail_header->fromAddress)) {
            $message_logger->setError(LogTypesEnum::ERROR_FROM_ADDRESS);
            $message_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_FROM_ADDRESS, ['message_id' => $_mid]));
            $message_logger->finishLog();
            continue;
        }

        $parser->setCompanyId($mail_header->fromAddress);
        if (empty($parser->company_id)) {
            $message_logger->setError(LogTypesEnum::ERROR_COMPANY_ID);
            $message_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_COMPANY_ID, ['message_id' => $_mid, 'email' => $mail_header->fromAddress]));
            $message_logger->finishLog();
            continue;
        }
 
        $message = $mailbox->getMail($_mid);
        $attachments = $message->getAttachments();
        

        if (!empty($attachments)) {
            foreach ($attachments as $attachment_data) {
                $attachment_logger = new Logger();
                $attachment_logger->setParentLogId($message_logger->getLogId());
                $attachment_logger->setType(LogTypesEnum::ATTACHMENT);

                $attachment_name = $attachment_data->name;
                $attachment_filepath = $attachment_data->__get('filePath');
                $result = $parser->createCompanyImportFile($attachment_name, $attachment_filepath);

                if ($result) {
                    
                    if ($parser->updatePreset()) {

                        $success_import = $parser->startImport($attachment_logger);

                        if ($success_import) {
                            $count_files ++;    
                        }else {
                            $attachment_logger->setError(LogTypesEnum::ERROR_IMPORT);
                            $attachment_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_IMPORT, ['preset_id' => $parser->getPresetId()]));
                            $attachment_logger->finishLog(); 
                        }
                    }else {
                        $attachment_logger->setError(LogTypesEnum::ERROR_UPDATE_PRESET);
                        $attachment_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_UPDATE_PRESET, ['preset_id' => $parser->getPresetId()]));
                        $attachment_logger->finishLog();
                    }
                }else {
                    $attachment_logger->setError(LogTypesEnum::ERROR_NO_CREATE_FILE);
                    $attachment_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_NO_CREATE_FILE, ['file' => $attachment_name, 'company_id' => $parser->company_id]));
                    $attachment_logger->finishLog();
                }
            }
        }else {
            $message_logger->setError(LogTypesEnum::ERROR_NO_ATTACHMENTS);
            $message_logger->setMess(LogManager::getTypeDesc(LogTypesEnum::ERROR_NO_ATTACHMENTS, ['message_id' => $_mid]));
            $message_logger->finishLog(); 
        }
        $message_logger->setMess(__("cp_email_parser.mail_success_processed", [ "[mess_id]" => $_mid]));
        $message_logger->finishLog();
    }

    if ($count_messages == 0) {
        $main_logger->setMess(__("cp_email_parser.no_mails"));
        $main_logger->finishLog();
    }else {
        $main_logger->setMess(__("cp_email_parser.success_parcing", [ "[c_mess]" => $count_messages, "[c_files]" => $count_files]));
        $main_logger->finishLog(); 
    }

    if (isset($_REQUEST['is_manually'])) {
        return [CONTROLLER_STATUS_REDIRECT, 'cp_email_parser.parsing_logs'];
    }

    exit;

}elseif ($mode == "parsing_logs") {

    $params = $_REQUEST;
    list($logs, $search) = LogManager::getLogRecords($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Tygh::$app['view']->assign('logs', $logs);
    Tygh::$app['view']->assign('search', $search);

}elseif ($mode == "log_events") {

    $params = $_REQUEST;

    if (!empty($params['log_id'])) {

        $log_events = LogManager::getChainOfEvents($params['log_id']);
        
        Tygh::$app['view']->assign('events', $log_events);
        Tygh::$app['view']->assign('id', $params['log_id']);   
    }
    
}