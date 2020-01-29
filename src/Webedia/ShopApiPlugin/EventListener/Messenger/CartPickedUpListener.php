<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\EventListener\Messenger;

use App\Webedia\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use App\Webedia\ShopApiPlugin\Event\CartPickedUp;
use App\Webedia\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartPickedUpListener
{
    /** @var LoggedInShopUserProviderInterface */
    private $loggedInShopUserProvider;

    /** @var MessageBusInterface */
    private $bus;

    public function __construct(LoggedInShopUserProviderInterface $loggedInShopUserProvider, MessageBusInterface $bus)
    {
        $this->loggedInShopUserProvider = $loggedInShopUserProvider;
        $this->bus = $bus;
    }

    public function __invoke(CartPickedUp $cartPickedUp): void
    {
        if (!$this->loggedInShopUserProvider->isUserLoggedIn()) {
            return;
        }

        $shopUser = $this->loggedInShopUserProvider->provide();
        $email = $shopUser->getCustomer()->getEmail();

        $this->bus->dispatch(new AssignCustomerToCart($cartPickedUp->orderToken(), $email));
    }
}
