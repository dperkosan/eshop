<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\ViewRepository\Product;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use App\Webedia\ShopApiPlugin\Factory\Product\PageViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Factory\Product\ProductReviewViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Model\PaginatorDetails;
use App\Webedia\ShopApiPlugin\Provider\SupportedLocaleProviderInterface;
use App\Webedia\ShopApiPlugin\View\Product\PageView;
use Webmozart\Assert\Assert;

final class ProductReviewsViewRepository implements ProductReviewsViewRepositoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var ProductReviewRepositoryInterface */
    private $productReviewRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductReviewViewFactoryInterface */
    private $productReviewViewFactory;

    /** @var PageViewFactoryInterface */
    private $pageViewFactory;

    /** @var SupportedLocaleProviderInterface */
    private $supportedLocaleProvider;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        ProductReviewRepositoryInterface $productReviewRepository,
        ProductRepositoryInterface $productRepository,
        ProductReviewViewFactoryInterface $productReviewViewFactory,
        PageViewFactoryInterface $pageViewFactory,
        SupportedLocaleProviderInterface $supportedLocaleProvider
    ) {
        $this->channelRepository = $channelRepository;
        $this->productReviewRepository = $productReviewRepository;
        $this->productRepository = $productRepository;
        $this->productReviewViewFactory = $productReviewViewFactory;
        $this->pageViewFactory = $pageViewFactory;
        $this->supportedLocaleProvider = $supportedLocaleProvider;
    }

    public function getByProductSlug(string $productSlug, string $channelCode, PaginatorDetails $paginatorDetails, ?string $localeCode): PageView
    {
        $channel = $this->getChannel($channelCode);
        $localeCode = $this->supportedLocaleProvider->provide($localeCode, $channel);

        $reviews = $this->productReviewRepository->findAcceptedByProductSlugAndChannel($productSlug, $localeCode, $channel);

        $paginatorDetails->addToParameters('slug', $productSlug);

        return $this->createProductReviewPage($reviews, $paginatorDetails);
    }

    public function getByProductCode(string $productCode, string $channelCode, PaginatorDetails $paginatorDetails): PageView
    {
        $channel = $this->getChannel($channelCode);

        $product = $this->productRepository->findOneByCode($productCode);
        Assert::notNull($product);
        Assert::true($product->hasChannel($channel));

        $reviews = $this->productReviewRepository->findBy(['reviewSubject' => $product->getId(), 'status' => ReviewInterface::STATUS_ACCEPTED]);

        $paginatorDetails->addToParameters('code', $productCode);

        return $this->createProductReviewPage($reviews, $paginatorDetails);
    }

    private function getChannel(string $channelCode): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        Assert::notNull($channel, sprintf('Channel with code %s has not been found.', $channelCode));

        return $channel;
    }

    private function createProductReviewPage(array $reviews, PaginatorDetails $paginatorDetails): PageView
    {
        $pagerfanta = new Pagerfanta(new ArrayAdapter($reviews));

        $pagerfanta->setMaxPerPage($paginatorDetails->limit());
        $pagerfanta->setCurrentPage($paginatorDetails->page());

        $pageView = $this->pageViewFactory->create($pagerfanta, $paginatorDetails->route(), $paginatorDetails->parameters());

        foreach ($pagerfanta->getCurrentPageResults() as $currentPageResult) {
            $pageView->items[] = $this->productReviewViewFactory->create($currentPageResult);
        }

        return $pageView;
    }
}
