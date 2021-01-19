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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

define('TM_DB_BACKUP', 'D');
define('TM_FILES_BACKUP', 'F');
define('TM_EXPORT', 'E');
define('TM_IMPORT', 'I');
define('TM_CLEAR_CACHE', 'C');
define('TM_CLEAR_TEMPLATES', 'T');
define('TM_THUMBNAILS_REGENERATION', 'R');
define('TM_CLEAR_LOGS', 'L');
define('TM_CUSTOM_SCRIPT', 'S');
define('TM_REGENERATE_SITEMAP', 'G');
define('TM_DROPBOX', 'B');
define('TM_FTP', 'P');
define('TM_OPTIMIZE_DATABASE', 'O');
define('TM_DATA_FEED', 'Z');



define('REGEXP_MINUTES', '/^[\*,\/\-0-9]+$/');
define('REGEXP_HOURS',   '/^[\*,\/\-0-9]+$/');
//define('REGEXP_DAYS', '/^[\*,\/\-\?LW0-9A-Za-z]+$/');
define('REGEXP_DAYS', '/^[\*,\/\-\?LW0-9]+$/');
//define('REGEXP_MONTHS', '/^[\*,\/\-0-9A-Z]+$/');
define('REGEXP_MONTHS', '/^[\*,\/\-0-9]+|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec$/');
//define('REGEXP_DWS', '/^(\*|[0-7](L?|#[1-5]))?([\/\,\-][0-7]+|sun|mon|tue|wed|thu|fri|sat)*$/');
define('REGEXP_DWS', '/^[\*,\/\-0-7]+|sun|mon|tue|wed|thu|fri|sat$/');

