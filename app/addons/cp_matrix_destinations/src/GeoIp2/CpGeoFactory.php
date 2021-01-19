<?php
namespace Tygh\Addons\CpMatrixDestinations\GeoIp2;
use Tygh\Registry;
use Tygh\Addons\CpMatrixDestinations\GeoIp2\Database\CpGeoReader;
use Tygh\Addons\CpMatrixDestinations\GeoIp2\Database;

Class CpGeoFactory {
    
    function __construct()
    {

        require_once(Registry::get('config.dir.addons'). 'cp_matrix_destinations/src/GeoIp2/vendor/autoload.php');
        $reader = new CpGeoReader(Registry::get('config.dir.addons'). 'cp_matrix_destinations/src/GeoIp2/GeoLite2-City.mmdb');
        return $reader;
    }
}