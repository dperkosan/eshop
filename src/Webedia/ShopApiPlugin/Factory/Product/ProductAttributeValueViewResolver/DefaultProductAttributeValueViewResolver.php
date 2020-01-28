<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class DefaultProductAttributeValueViewResolver implements ProductAttributeValueViewResolverInterface
{
    public function getValue(ProductAttributeValueInterface $productAttributeValue, string $localeCode)
    {
        return $productAttributeValue->getValue();
    }
}
