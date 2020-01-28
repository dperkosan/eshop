<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\EventListener\Messenger;

use App\Webedia\ShopApiPlugin\Command\Customer\SendOrderConfirmation;
use App\Webedia\ShopApiPlugin\Event\OrderCompleted;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCompletedListener
{
    /** @var MessageBusInterface */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(OrderCompleted $orderCompleted): void
    {
        $this->bus->dispatch(new SendOrderConfirmation($orderCompleted->orderToken()));
    }
}
