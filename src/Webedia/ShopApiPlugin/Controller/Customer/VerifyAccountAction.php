<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use App\Webedia\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use App\Webedia\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class VerifyAccountAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CommandProviderInterface */
    private $verifyAccountCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CommandProviderInterface $verifyAccountCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->verifyAccountCommandProvider = $verifyAccountCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        $validationResults = $this->verifyAccountCommandProvider->validate($request);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        $this->bus->dispatch($this->verifyAccountCommandProvider->getCommand($request));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
