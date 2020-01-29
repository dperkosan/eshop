<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface ProductInCartChannelCheckerInterface
{
    public function isProductInCartChannel(ProductInterface $product, OrderInterface $cart): bool;
}
