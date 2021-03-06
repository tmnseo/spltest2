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

namespace Tygh\Addons\CpInvoicesForAccounting;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Tools\SecurityHelper;
use Tygh\Registry;
use Tygh\Tygh;


/**
 * Class ServiceProvider is intended to register services and components
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    
    public function register(Container $app)
    {   
        
    }

    
    public static function cronPass()
    {
        return Registry::get('addons.cp_invoices_for_accounting.cron_pass');
    }

    public static function folderName()
    {
        return Registry::get('addons.cp_invoices_for_accounting.folder_name');
    }    
}

