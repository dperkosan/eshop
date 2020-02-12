<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Product;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use App\Webedia\ShopApiPlugin\ViewRepository\Product\ProductVariantDetailsViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ShowProductVariantDetailsByCodeAction
{
    /** @var ProductVariantDetailsViewRepositoryInterface */
    private $productCatalog;

    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        ProductVariantDetailsViewRepositoryInterface $productCatalog,
        ViewHandlerInterface $viewHandler,
        ChannelContextInterface $channelContext
    ) {
        $this->productCatalog = $productCatalog;
        $this->viewHandler = $viewHandler;
        $this->channelContext = $channelContext;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $channel = $this->channelContext->getChannel();

            return $this->viewHandler->handle(View::create($this->productCatalog->findProductByVariantCode(
                $request->attributes->get('code'),
                $channel->getCode(),
                $request->query->get('locale')
            ), Response::HTTP_OK));
        } catch (ChannelNotFoundException $exception) {
            throw new NotFoundHttpException('Channel has not been found.');
        } catch (\InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
