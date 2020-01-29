<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use App\Webedia\ShopApiPlugin\Factory\Customer\CustomerViewFactoryInterface;
use App\Webedia\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class LoggedInCustomerDetailsAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    /** @var CustomerViewFactoryInterface */
    private $customerViewFactory;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        LoggedInShopUserProviderInterface $loggedInShopUserProvider,
        CustomerViewFactoryInterface $customerViewFactory
    ) {
        $this->viewHandler = $viewHandler;
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
        $this->customerViewFactory = $customerViewFactory;
    }

    public function __invoke(Request $request): Response
    {
        if (!$this->loggedInShopUserProvider->isUserLoggedIn()) {
            return $this->viewHandler->handle(View::create(null, Response::HTTP_UNAUTHORIZED));
        }

        $customer = $this->loggedInShopUserProvider->provide()->getCustomer();
        Assert::notNull($customer);

        return $this->viewHandler->handle(View::create($this->customerViewFactory->create($customer)));
    }
}
