<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory;

use App\Webedia\ShopApiPlugin\View\PriceView;

interface PriceViewFactoryInterface
{
    public function create(int $price, string $currency): PriceView;
}
