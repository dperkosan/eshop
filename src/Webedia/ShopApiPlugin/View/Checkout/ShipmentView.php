<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\View\Checkout;

use App\Webedia\ShopApiPlugin\View\Cart\ShippingMethodView;

class ShipmentView
{
    /** @var string */
    public $state;

    /** @var ShippingMethodView */
    public $method;

    public function __construct()
    {
        $this->method = new ShippingMethodView();
    }
}
