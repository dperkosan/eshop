<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use App\Webedia\ShopApiPlugin\Shipping\ShippingCost;
use App\Webedia\ShopApiPlugin\View\Cart\EstimatedShippingCostView;

interface EstimatedShippingCostViewFactoryInterface
{
    public function create(ShippingCost $shippingCost): EstimatedShippingCostView;
}
