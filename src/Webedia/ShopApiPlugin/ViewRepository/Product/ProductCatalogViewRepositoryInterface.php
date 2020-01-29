<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use App\Webedia\ShopApiPlugin\Model\PaginatorDetails;
use App\Webedia\ShopApiPlugin\View\Product\PageView;

interface ProductCatalogViewRepositoryInterface
{
    public function findByTaxonSlug(string $taxonSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;

    public function findByTaxonCode(string $taxonCode, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView;
}
