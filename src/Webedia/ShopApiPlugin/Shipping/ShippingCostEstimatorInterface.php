<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Shipping;

interface ShippingCostEstimatorInterface
{
    public function estimate(string $cartToken, string $countryCode, ?string $provinceCode): ShippingCost;
}
