<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductAttributeValueView;

interface ProductAttributeValueViewFactoryInterface
{
    public function create(ProductAttributeValueInterface $productAttributeValue, string $localeCode): ProductAttributeValueView;
}
