<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use App\Webedia\ShopApiPlugin\View\Product\ProductView;

interface ProductDetailsViewRepositoryInterface
{
    public function findOneBySlug(string $productSlug, string $channelCode, ?string $localeCode): ProductView;

    public function findOneByCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}
