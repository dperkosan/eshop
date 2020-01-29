<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use App\Webedia\ShopApiPlugin\Factory\Product\ProductViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use App\Webedia\ShopApiPlugin\View\Product\ProductListView;
use Webmozart\Assert\Assert;

final class ProductLatestViewRepository implements ProductLatestViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductViewFactoryInterface */
    private $productViewFactory;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductRepositoryInterface $productRepository,
        ProductViewFactoryInterface $productViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->productRepository = $productRepository;
        $this->productViewFactory = $productViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function getLatestProducts(string $channelCode, ?string $localeCode, int $count): ProductListView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);
        $latestProducts = $this->productRepository->findLatestByChannel($channel, $localeCode, $count);

        Assert::notNull($latestProducts, sprintf('Latest Products not found in %s locale.', $localeCode));

        $productListView = new ProductListView();

        foreach ($latestProducts as $product) {
            $productListView->items[] = $this->productViewFactory->create($product, $channel, $localeCode);
        }

        return $productListView;
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }
}
