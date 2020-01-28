<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use App\Webedia\ShopApiPlugin\Exception\ViewCreationException;
use App\Webedia\ShopApiPlugin\View\Product\ProductVariantView;

interface ProductVariantViewFactoryInterface
{
    /** @throws ViewCreationException */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): ProductVariantView;
}
