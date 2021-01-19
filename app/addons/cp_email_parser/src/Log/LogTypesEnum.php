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

namespace Tygh\Addons\CpEmailParser\Log;

class LogTypesEnum {

    const ERROR_CRON_PASS      = 'A';
    const ERROR_DIR_CREATE     = 'B';
    const ERROR_IMAP_CONNECT   = 'C';
    const ERROR_FROM_ADDRESS   = 'D';
    const ERROR_COMPANY_ID     = 'E';
    const ERROR_NO_ATTACHMENTS = 'F';
    const ERROR_NO_CREATE_FILE = 'G';
    const ERROR_UPDATE_PRESET  = 'H';
    const ERROR_IMPORT         = 'I';

    const SUCCESS_PARSING      = 'M'; // Main parser log
    const MESSAGE              = 'X';
    const ATTACHMENT           = 'Z';
    
}