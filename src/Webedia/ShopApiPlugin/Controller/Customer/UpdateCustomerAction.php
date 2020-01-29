<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use App\Webedia\ShopApiPlugin\CommandProvider\ShopUserBasedCommandProviderInterface;
use App\Webedia\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Factory\ValidationErrorViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Provider\LoggedInShopUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class UpdateCustomerAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ValidationErrorViewFactoryInterface */
    private $validationErrorViewFactory;

    /** @var CustomerViewFactoryInterface */
    private $customerViewFactory;

    /** @var LoggedInShopUserProvider */
    private $loggedInUserProvider;

    /** @var ShopUserBasedCommandProviderInterface */
    private $updateCustomerCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ValidationErrorViewFactoryInterface $validationErrorViewFactory,
        CustomerViewFactoryInterface $customerViewFactory,
        LoggedInShopUserProvider $loggedInUserProvider,
        ShopUserBasedCommandProviderInterface $updateCustomerCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->validationErrorViewFactory = $validationErrorViewFactory;
        $this->customerViewFactory = $customerViewFactory;
        $this->loggedInUserProvider = $loggedInUserProvider;
        $this->updateCustomerCommandProvider = $updateCustomerCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        if (!$this->loggedInUserProvider->isUserLoggedIn()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        /** @var ShopUserInterface $user */
        $user = $this->loggedInUserProvider->provide();

        $validationResults = $this->updateCustomerCommandProvider->validate($request, $user, null, ['sylius_customer_profile_update']);
        if (0 !== count($validationResults)) {
            return $this->viewHandler->handle(View::create(
                $this->validationErrorViewFactory->create($validationResults),
                Response::HTTP_BAD_REQUEST
            ));
        }

        $this->bus->dispatch($this->updateCustomerCommandProvider->getCommand($request, $user));

        /** @var CustomerInterface|null $customer */
        $customer = $this->loggedInUserProvider->provide()->getCustomer();
        Assert::notNull($customer);

        return $this->viewHandler->handle(View::create(
            $this->customerViewFactory->create($customer),
            Response::HTTP_OK
        ));
    }
}
