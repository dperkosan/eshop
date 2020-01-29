<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Checkout;

use Sylius\Component\Core\Model\ShipmentInterface;
use App\Webedia\ShopApiPlugin\View\Checkout\ShipmentView;

interface ShipmentViewFactoryInterface
{
    public function create(ShipmentInterface $shipment, string $locale): ShipmentView;
}
