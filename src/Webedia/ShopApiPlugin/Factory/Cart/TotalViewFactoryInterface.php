<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use App\Webedia\ShopApiPlugin\View\Cart\TotalsView;

interface TotalViewFactoryInterface
{
    public function create(OrderInterface $cart): TotalsView;
}
