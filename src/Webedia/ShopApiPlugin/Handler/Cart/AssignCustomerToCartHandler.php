<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use App\Webedia\ShopApiPlugin\Command\Cart\AssignCustomerToCart;
use App\Webedia\ShopApiPlugin\Provider\CustomerProviderInterface;
use Webmozart\Assert\Assert;

final class AssignCustomerToCartHandler
{
    /** @var OrderRepositoryInterface */
    private $cartRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var CustomerProviderInterface */
    private $customerProvider;

    public function __construct(
        OrderRepositoryInterface $cartRepository,
        OrderProcessorInterface $orderProcessor,
        CustomerProviderInterface $customerProvider
    ) {
        $this->cartRepository = $cartRepository;
        $this->customerProvider = $customerProvider;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(AssignCustomerToCart $assignOrderToCustomer): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartRepository->findOneBy(['tokenValue' => $assignOrderToCustomer->orderToken()]);

        Assert::notNull($cart, sprintf('Order with %s token has not been found.', $assignOrderToCustomer->orderToken()));

        $customer = $this->customerProvider->provide($assignOrderToCustomer->email());

        $cart->setCustomer($customer);

        $this->orderProcessor->process($cart);
    }
}
