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

namespace Tygh\Addons\CpEmailParser;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Tools\SecurityHelper;
use Tygh\Registry;
use Tygh\Addons\CpEmailParser\MailParser\MailParser;
use Tygh\Tygh;


/**
 * Class ServiceProvider is intended to register services and components
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    const HOST = '{imap.yandex.ru:993/imap/ssl}INBOX';
    public function register(Container $app)
    {   
        $app['addons.cp_email_parser.mail_parser'] = function (Container $app) {
            return new MailParser($app);
        };

        $app['addons.cp_email_parser.mail_parser_files_path'] = Registry::get('config.dir.files') . "email_files";
    }

    
    /**
    * @return MailParser
    */
    public static function getMailParser() 
    {
        return Tygh::$app['addons.cp_email_parser.mail_parser'];
    }

    public static function mailParserFilesDirectory()
    {
        return Tygh::$app['addons.cp_email_parser.mail_parser_files_path'];
    }

    public static function mailUser()
    {
        return Registry::get('addons.cp_email_parser.mail_user');
    }
    
    public static function mailPassword()
    {
        return Registry::get('addons.cp_email_parser.mail_pass');
    }

    public static function cronPass()
    {
        return Registry::get('addons.cp_email_parser.cron_pass');
    }    
}

