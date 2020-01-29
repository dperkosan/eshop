<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Cart;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use App\Webedia\ShopApiPlugin\Command\Cart\RemoveItemFromCart;
use App\Webedia\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use App\Webedia\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use App\Webedia\ShopApiPlugin\ViewRepository\Cart\CartViewRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveItemFromCartAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CartViewRepositoryInterface */
    private $cartQuery;

    /** @var CommandProviderInterface */
    private $removeItemFromCartCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CartViewRepositoryInterface $cartQuery,
        CommandProviderInterface $removeItemFromCartCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->cartQuery = $cartQuery;
        $this->removeItemFromCartCommandProvider = $removeItemFromCartCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        $validationResults = $this->removeItemFromCartCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        /** @var RemoveItemFromCart $removeItemFromCartCommand */
        $removeItemFromCartCommand = $this->removeItemFromCartCommandProvider->getCommand($request);

        $this->bus->dispatch($removeItemFromCartCommand);

        try {
            return $this->viewHandler->handle(View::create(
                $this->cartQuery->getOneByToken($removeItemFromCartCommand->orderToken()),
                Response::HTTP_OK
            ));
        } catch (\InvalidArgumentException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
