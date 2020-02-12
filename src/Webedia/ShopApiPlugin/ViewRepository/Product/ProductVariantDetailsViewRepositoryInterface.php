<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use App\Webedia\ShopApiPlugin\View\Product\ProductView;

interface ProductVariantDetailsViewRepositoryInterface
{
    public function findProductByVariantCode(string $productCode, string $channelCode, ?string $localeCode): ProductView;
}
