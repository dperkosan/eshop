<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductAttributeValueView;

interface ProductAttributeValuesViewFactoryInterface
{
    /**
     * @param array|AttributeValueInterface[] $attributeValues
     *
     * @return array|ProductAttributeValueView[]
     */
    public function create(array $attributeValues, string $locale): array;
}
