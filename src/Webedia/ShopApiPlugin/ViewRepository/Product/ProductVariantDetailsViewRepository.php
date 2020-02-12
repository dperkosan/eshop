<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use App\Webedia\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductView;
use Webmozart\Assert\Assert;

final class ProductVariantDetailsViewRepository implements ProductVariantDetailsViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productViewFactory = $productViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function findProductByVariantCode(string $productCode, string $channelCode, ?string $localeCode): ProductView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);

        $productVariant = $this->productVariantRepository->findOneByCode($productCode);
        $product = $productVariant->getProduct();
        
        Assert::notNull($product, sprintf('Product with code %s has not been found.', $productCode));
        Assert::true($product->hasChannel($channel), sprintf('Product with code %s has not been found for channel %s.', $productCode, $channelCode));

        return $this->productViewFactory->create($product, $channel, $localeCode);
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }
}
