<?php

declare(strict_types=1);

namespace App\Webedia\ShopApiPlugin\Controller\Customer;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use App\Webedia\ShopApiPlugin\CommandProvider\ChannelBasedCommandProviderInterface;
use App\Webedia\ShopApiPlugin\CommandProvider\CommandProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RequestPasswordResettingAction
{
    /** @var ViewHandlerInterface */
    private $viewHandler;

    /** @var MessageBusInterface */
    private $bus;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var CommandProviderInterface */
    private $generateResetPasswordTokenCommandProvider;

    /** @var ChannelBasedCommandProviderInterface */
    private $sendResetPasswordTokenCommandProvider;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        MessageBusInterface $bus,
        ChannelContextInterface $channelContext,
        CommandProviderInterface $generateResetPasswordTokenCommandProvider,
        ChannelBasedCommandProviderInterface $sendResetPasswordTokenCommandProvider
    ) {
        $this->viewHandler = $viewHandler;
        $this->bus = $bus;
        $this->channelContext = $channelContext;
        $this->generateResetPasswordTokenCommandProvider = $generateResetPasswordTokenCommandProvider;
        $this->sendResetPasswordTokenCommandProvider = $sendResetPasswordTokenCommandProvider;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->bus->dispatch($this->generateResetPasswordTokenCommandProvider->getCommand($request));
        $this->bus->dispatch($this->sendResetPasswordTokenCommandProvider->getCommand($request, $channel));

        return $this->viewHandler->handle(View::create(null, Response::HTTP_NO_CONTENT));
    }
}
