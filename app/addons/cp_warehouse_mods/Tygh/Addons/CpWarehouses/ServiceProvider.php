<?php

namespace Tygh\Addons\CpWarehouses;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Tygh;

use Tygh\Addons\CpWarehouses\CpManager;

class ServiceProvider implements ServiceProviderInterface
{
    /** @inheritdoc */
    public function register(Container $app)
    {
        $app['addons.cpwarehouses.manager'] = function(Container $app) {
            return new CpManager($app['db'], DESCR_SL);
        };
    }
}