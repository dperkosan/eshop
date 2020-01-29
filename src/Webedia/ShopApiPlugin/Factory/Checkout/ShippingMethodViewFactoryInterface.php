<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use App\Webedia\ShopApiPlugin\View\Cart\ShippingMethodView;

interface ShippingMethodViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale, string $currency): ShippingMethodView;

    public function createWithShippingMethod(
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        string $locale,
        string $currency
    ): ShippingMethodView;
}
