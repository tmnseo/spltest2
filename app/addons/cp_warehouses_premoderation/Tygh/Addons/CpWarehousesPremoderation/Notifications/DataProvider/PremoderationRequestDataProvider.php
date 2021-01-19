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

namespace Tygh\Addons\CpWarehousesPremoderation\Notifications\DataProviders;

use Tygh\Exceptions\DeveloperException;
use Tygh\Notifications\DataProviders\BaseDataProvider;

class PremoderationRequestDataProvider extends BaseDataProvider
{
    protected $request_data = [];

    public function __construct(array $data)
    {
        if (empty($data['premoderation_request_data'])) {
            throw new DeveloperException('Store data not found.');
        }

        $this->request_data = $data['premoderation_request_data'];

        $data['warehouse'] = $this->request_data['warehouse'];
        $data['vendor'] = !empty($this->request_data['vendor']) ? $this->request_data['vendor'] : null;
        $data['premoderation_status'] = $this->request_data['premoderation_status'];
        $data['reason'] = !empty($this->request_data['reason']) ? $this->request_data['reason'] : null;

        parent::__construct($data);
    }
}