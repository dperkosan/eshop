<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use App\Webedia\ShopApiPlugin\Model\PaginatorDetails;
use App\Webedia\ShopApiPlugin\ViewRepository\Product\ProductCatalogViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductCatalogByTaxonSlugAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ProductCatalogViewRepositoryInterface */
    private $productCatalogQuery;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        ProductCatalogViewRepositoryInterface $productCatalogQuery,
        ChannelContextInterface $channelContext
    ) {
        $this->viewHandler = $viewHandler;
        $this->productCatalogQuery = $productCatalogQuery;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productCatalogQuery->findByTaxonSlug(
                $request->attributes->get('taxonSlug'),
                $channel->getCode(),
                new PaginatorDetails($request->attributes->get('_route'), $request->query->all()),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
