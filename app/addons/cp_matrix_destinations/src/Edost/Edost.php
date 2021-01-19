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

namespace Tygh\Addons\CpMatrixDestinations\Edost;

use Tygh\Addons\CpMatrixDestinations\City\City;
use Tygh\Addons\CpMatrixDestinations\Stores\Stores;
use Tygh\Http;
use Tygh\Languages\Languages;

class Edost
{

    public static function _getRates($response)
    {
        $return = array();
        $xml = @simplexml_load_string($response);
        $services = fn_get_schema('edost', 'services', 'php', true);

        if (!empty($xml)) {
            foreach ($xml->tarif as $shipment) {
                $strah = (int) $shipment->strah;

                $tarif_id = (int) $shipment->id;
                $service_code = $tarif_id * 2 + $strah + 299;
                $tarifs[$tarif_id] = $service_code;

                $return[$service_code] = array(
                    'price' => (string) $shipment->price,
                    'pricecash' => (string) $shipment->pricecash,
                    'transfer' => (string) $shipment->transfer,
                    'strah' => (string) $shipment->strah,
                    'id' => $tarif_id,
                    'day' => (string) $shipment->day,
                    'company' => (string) $shipment->company,
                    'name' => (string) $shipment->name
                );

                if (!empty($shipment->pickpointmap)) {
                    $return[$service_code]['city_pickpoint'] = (string) $shipment->pickpointmap;
                }
            }

            if (!empty($xml->office)) {
                foreach ($xml->office as $office) {
                    $office_id = (string) $office->id;
                    $shipment_ids = explode(',', (string) $office->to_tarif);

                    foreach ($shipment_ids as $id) {
                        $service_code_insurance = $tarifs[$id];
                        $service_code = empty($services[$tarifs[$id]]['no_insurance_variant']) ? 0 : $services[$tarifs[$id]]['no_insurance_variant'];

                        foreach (array($service_code, $service_code_insurance) as $service_code_key) {
                            if (!empty($return[$service_code_key])) {
                                $return[$service_code_key]['office'][$office_id] = array(
                                    'office_id' => $office_id,
                                    'name' => (string) $office->name,
                                    'address' => (string) $office->address,
                                    'tel' => (string) $office->tel,
                                    'schedule' => (string) $office->schedule,
                                    'gps' => (string) $office->gps,
                                );
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }
}