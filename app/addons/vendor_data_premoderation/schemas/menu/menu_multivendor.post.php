<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */

if (!isset($schema['central']['products']['items']['products']['subitems'])) {
    $schema['central']['products']['items']['products']['subitems'] = [];
}

$schema['central']['products']['items']['products']['subitems']['vendor_data_premoderation.require_approval'] = [
    'href'     => 'products.manage?status=' . ProductStatuses::REQUIRES_APPROVAL,
    'position' => 10,
];

$schema['central']['products']['items']['products']['subitems']['vendor_data_premoderation.require_vendor_action'] = [
    'href'     => 'products.manage?status=' . ProductStatuses::DISAPPROVED,
    'position' => 20,
];

return $schema;
