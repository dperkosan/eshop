<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use App\Webedia\ShopApiPlugin\View\Cart\CartSummaryView;

interface CartViewFactoryInterface
{
    public function create(OrderInterface $cart, string $localeCode): CartSummaryView;
}
