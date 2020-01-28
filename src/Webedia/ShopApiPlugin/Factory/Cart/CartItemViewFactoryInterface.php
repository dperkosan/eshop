<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use App\Webedia\ShopApiPlugin\View\ItemView;

interface CartItemViewFactoryInterface
{
    public function create(OrderItemInterface $item, ChannelInterface $channel, string $locale): ItemView;
}
