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

namespace Tygh\Addons\CpVendorWarehouses\Api;

use Tygh\Addons\CpVendorWarehouses\ServiceProvider;

class StaticApi {
        
    private static $api_url = "https://static-maps.yandex.ru/1.x/?";
        
    public static function getMapHref($warehouse)
    {   
        $width = ServiceProvider::mapWidth();
        $height = ServiceProvider::mapHeight();
        $zoom = ServiceProvider::mapZoom();

        $params = [
            'll' => $warehouse->getCoordinates(),
            '&l' => 'map',
            '&z' => $zoom,
            '&size' => $width . ',' . $height,
        ];
        
        $href = self::$api_url;
        
        foreach ($params as $key => $value) {
            $href .= $key . "=" . $value;
        }
        
        return $href;
    }
}
