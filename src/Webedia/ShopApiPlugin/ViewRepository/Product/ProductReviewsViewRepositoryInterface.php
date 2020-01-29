<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use App\Webedia\ShopApiPlugin\Model\PaginatorDetails;
use App\Webedia\ShopApiPlugin\View\Product\PageView;

interface ProductReviewsViewRepositoryInterface
{
    public function getByProductSlug(string $productSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function getByProductCode(string $productCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView;
}
