<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Handler\Cart;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use App\Webedia\ShopApiPlugin\Command\Cart\CompleteOrder;
use App\Webedia\ShopApiPlugin\Event\OrderCompleted;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class CompleteOrderHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        MessageBusInterface $messageBus
    ) {
        $this->orderRepository = $orderRepository;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->messageBus = $messageBus;
    }

    public function __invoke(CompleteOrder $completeOrder): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $completeOrder->orderToken()]);

        Assert::notNull($order, sprintf('Order with %s token has not been found.', $completeOrder->orderToken()));

        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);

        Assert::true($stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE), sprintf('Order with %s token cannot be completed.', $completeOrder->orderToken()));

        $order->setNotes($completeOrder->notes());

        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->messageBus->dispatch(new OrderCompleted($order->getTokenValue()), [new DispatchAfterCurrentBusStamp()]);
    }
}
