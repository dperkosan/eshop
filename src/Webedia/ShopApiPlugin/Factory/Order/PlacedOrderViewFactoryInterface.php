<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Order;

use Sylius\Component\Core\Model\OrderInterface;
use App\Webedia\ShopApiPlugin\View\Order\PlacedOrderView;

interface PlacedOrderViewFactoryInterface
{
    public function create(OrderInterface $order, string $localeCode): PlacedOrderView;
}
