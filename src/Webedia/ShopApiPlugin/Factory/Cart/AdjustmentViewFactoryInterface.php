<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Cart;

use Sylius\Component\Order\Model\AdjustmentInterface;
use App\Webedia\ShopApiPlugin\View\Cart\AdjustmentView;

interface AdjustmentViewFactoryInterface
{
    public function create(AdjustmentInterface $adjustment, int $additionalAmount, string $currency): AdjustmentView;
}
