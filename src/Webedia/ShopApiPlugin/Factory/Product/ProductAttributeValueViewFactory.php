<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use App\Webedia\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver\ProductAttributeValueViewResolverInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductAttributeValueView;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductAttributeValueViewFactory implements ProductAttributeValueViewFactoryInterface
{
    /** @var string */
    private $productAttributeValueViewClass;

    /** @var ServiceLocator */
    private $productAttributeValueViewResolversLocator;

    public function __construct(
        string $productAttributeValueViewClass,
        ServiceLocator $productAttributeValueViewResolversLocator
    ) {
        $this->productAttributeValueViewClass = $productAttributeValueViewClass;
        $this->productAttributeValueViewResolversLocator = $productAttributeValueViewResolversLocator;
    }

    public function create(ProductAttributeValueInterface $productAttributeValue, string $localeCode): ProductAttributeValueView
    {
        /** @var ProductAttributeValueView $productAttributeValueView */
        $productAttributeValueView = new $this->productAttributeValueViewClass();
        $productAttributeValueView->code = $productAttributeValue->getCode();
        $productAttributeValueView->type = $productAttributeValue->getType();

        /** @var ProductAttributeValueViewResolverInterface $valueResolver */
        $valueResolver = $this->productAttributeValueViewResolversLocator->get($productAttributeValue->getType());
        $productAttributeValueView->value = $valueResolver->getValue($productAttributeValue, $localeCode);

        $productAttribute = $productAttributeValue->getAttribute();
        $productAttributeTranslation = $productAttribute->getTranslation($localeCode);
        $productAttributeValueView->name = $productAttributeTranslation->getName();

        return $productAttributeValueView;
    }
}
