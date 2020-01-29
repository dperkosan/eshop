<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Pagerfanta\Pagerfanta;
use App\Webedia\ShopApiPlugin\View\Product\PageView;

interface PageViewFactoryInterface
{
    public function create(Pagerfanta $pagerfanta, string $route, array $parameters): PageView;
}
