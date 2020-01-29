<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\AddressBook;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use App\Webedia\ShopApiPlugin\CommandProvider\ShopUserBasedCommandProviderInterface;
use App\Webedia\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SetDefaultAddressAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    /** @var ShopUserBasedCommandProviderInterface */
    private $setDefaultAddressCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        LoggedInShopUserProviderInterface $loggedInUserProvider,
        ShopUserBasedCommandProviderInterface $setDefaultAddressCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->setDefaultAddressCommandProvider = $setDefaultAddressCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        try {
            /** @var ShopUserInterface $user */
            $user = $this->loggedInUserProvider->provide();
        } catch (TokenNotFoundException $exception) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $validationResults = $this->setDefaultAddressCommandProvider->validate($request, $user);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        if ($user->getCustomer() !== null) {
            $this->bus->dispatch($this->setDefaultAddressCommandProvider->getCommand($request, $user));

            return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
        }

        return $this->viewHandler->handle(
            View::create(['message' => 'The user is not a customer'], Response::HTTP_BAD_REQUEST)
        );
    }
}
