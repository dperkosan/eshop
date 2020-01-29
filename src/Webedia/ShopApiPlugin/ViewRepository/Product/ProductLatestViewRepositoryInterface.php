<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use App\Webedia\ShopApiPlugin\View\Product\ProductListView;

interface ProductLatestViewRepositoryInterface
{
    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView;
}
