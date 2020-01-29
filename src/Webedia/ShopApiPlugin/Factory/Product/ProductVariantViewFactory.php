<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Factory\Product;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use App\Webedia\ShopApiPlugin\Exception\ViewCreationException;
use App\Webedia\ShopApiPlugin\Factory\PriceViewFactoryInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductVariantView;

final class ProductVariantViewFactory implements ProductVariantViewFactoryInterface
{
    /** @var PriceViewFactoryInterface */
    private $priceViewFactory;

    /** @var string */
    private $productVariantViewClass;

    public function __construct(PriceViewFactoryInterface $priceViewFactory, string $productVariantViewClass)
    {
        $this->priceViewFactory = $priceViewFactory;
        $this->productVariantViewClass = $productVariantViewClass;
    }

    /** {@inheritdoc} */
    public function create(ProductVariantInterface $variant, ChannelInterface $channel, string $locale): ProductVariantView
    {
        /** @var ProductVariantView $variantView */
        $variantView = new $this->productVariantViewClass();

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        if (null === $channelPricing) {
            throw new ViewCreationException('Variant does not have pricing.');
        }

        $variantView->code = $variant->getCode();
        $variantView->name = $variant->getTranslation($locale)->getName();
        $variantView->price = $this->priceViewFactory->create(
            $channelPricing->getPrice(),
            $channel->getBaseCurrency()->getCode()
        );

        $originalPrice = $channelPricing->getOriginalPrice();
        if (null !== $originalPrice) {
            $variantView->originalPrice = $this->priceViewFactory->create(
                $originalPrice,
                $channel->getBaseCurrency()->getCode()
            );
        }

        foreach ($variant->getOptionValues() as $optionValue) {
            $variantView->axis[] = $optionValue->getCode();
            $variantView->nameAxis[$optionValue->getCode()] = sprintf(
                '%s %s',
                $optionValue->getOption()->getTranslation($locale)->getName(),
                $optionValue->getTranslation($locale)->getValue()
            );
        }

        return $variantView;
    }
}
