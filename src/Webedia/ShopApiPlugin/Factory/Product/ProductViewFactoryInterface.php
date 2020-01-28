<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductView;

interface ProductViewFactoryInterface
{
    public function create(ProductInterface $product, ChannelInterface $channel, string $locale): ProductView;
}
