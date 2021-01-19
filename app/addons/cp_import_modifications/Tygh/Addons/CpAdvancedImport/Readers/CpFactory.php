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

namespace Tygh\Addons\CpAdvancedImport\Readers;

use Tygh\Addons\AdvancedImport\Readers\Factory;

class CpFactory extends Factory
{   
    protected function getReaderClass($extension)
    {   
        $extension = ($extension == 'xls') ? 'xlsx' : $extension; 
        if (class_exists('\Tygh\Addons\CpAdvancedImport\Readers\\' . fn_camelize(strtolower($extension)))) {
            return '\Tygh\Addons\CpAdvancedImport\Readers\\' . fn_camelize(strtolower($extension));
        }elseif (class_exists('\Tygh\Addons\AdvancedImport\Readers\\' . fn_camelize(strtolower($extension)))) {
            return '\Tygh\Addons\AdvancedImport\Readers\\' . fn_camelize(strtolower($extension));
        }else {
            return "";
        }
    }
}